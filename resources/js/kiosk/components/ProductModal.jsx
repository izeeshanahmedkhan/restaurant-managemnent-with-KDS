import { useState, useMemo, useEffect } from 'react';
import { useApp } from '../context/AppContext';
import { cartService } from '../services/cartService';

const ProductModal = ({ product, onClose }) => {
  const { state, dispatch, addToCart, updateCartItem, removeFromCart } = useApp();
  const [selectedModifiers, setSelectedModifiers] = useState({});
  const [quantity, setQuantity] = useState(1);
  
  // Check if we're editing an existing cart item
  const isEditMode = !!state.editingCartItem;
  const editingItem = state.editingCartItem;

  // Pre-fill form when editing
  useEffect(() => {
    if (isEditMode && editingItem && product) {
      setQuantity(editingItem.quantity);
      
      // Pre-select variations
      const modifiers = {};
      if (editingItem.variation_details && product.variations) {
        editingItem.variation_details.forEach((variationDetail, index) => {
          if (variationDetail.selected_options && variationDetail.selected_options.length > 0) {
            const groupId = `variation_${index}`;
            const productVariation = product.variations[index];
            
            if (productVariation && productVariation.options) {
              // Find the matching option objects from the product
              const selectedOptionObjects = variationDetail.selected_options.map(selectedOption => {
                return productVariation.options.find(option => 
                  option.label === selectedOption.label || option.id === selectedOption.id
                );
              }).filter(Boolean); // Remove any undefined matches
              
              if (selectedOptionObjects.length > 0) {
                if (variationDetail.type === 'single' || productVariation.type === 'single') {
                  modifiers[groupId] = selectedOptionObjects[0];
                } else {
                  modifiers[groupId] = selectedOptionObjects;
                }
              }
            }
          }
        });
      }
      
      // Pre-select add-ons
      if (editingItem.addon_details && product.add_ons) {
        // Find the matching addon objects from the product
        const selectedAddonObjects = editingItem.addon_details.map(addonDetail => {
          return product.add_ons.find(addon => 
            addon.name === addonDetail.name || addon.id === addonDetail.id
          );
        }).filter(Boolean); // Remove any undefined matches
        
        if (selectedAddonObjects.length > 0) {
          modifiers['addons'] = selectedAddonObjects;
        }
      }
      
      setSelectedModifiers(modifiers);
    }
  }, [isEditMode, editingItem, product]);

  // Transform API data to expected format
  const modifierGroups = useMemo(() => {
    const groups = [];
    
    // Variations groups (show first)
    if (product.variations && product.variations.length > 0) {
      product.variations.forEach((variation, index) => {
        groups.push({
          id: `variation_${index}`,
          name: variation.name || 'Variation',
          type: variation.type || 'single',
          required: variation.required || false,
          min: variation.min || 1,
          max: variation.max || 1,
          options: variation.options ? variation.options.map(option => ({
            id: option.id,
            label: option.label,
            delta: option.delta || 0,
            type: 'variation'
          })) : []
        });
      });
    }
    
    // Add-ons group (show after variations)
    if (product.add_ons && product.add_ons.length > 0) {
      groups.push({
        id: 'addons',
        name: 'Add-ons',
        type: 'multi',
        required: false,
        options: product.add_ons.map(addon => ({
          id: addon.id,
          label: addon.name,
          delta: addon.price || 0,
          type: 'addon'
        }))
      });
    }
    
    // Choice options groups
    if (product.choice_options && product.choice_options.length > 0) {
      product.choice_options.forEach((option, index) => {
        groups.push({
          id: `choice_${index}`,
          name: option.title,
          type: option.type === 'multi' ? 'multi' : 'single',
          required: option.required || false,
          min: option.min || 0,
          max: option.max || Infinity,
          options: option.options.map(opt => ({
            id: opt.id,
            label: opt.label,
            delta: opt.delta || 0
          }))
        });
      });
    }
    
    return groups;
  }, [product]);

  const calculatePrice = () => {
    let basePrice = product.price || 0;
    let modifierPrice = 0;

    Object.values(selectedModifiers).forEach(modifier => {
      if (Array.isArray(modifier)) {
        modifier.forEach(option => {
          if (option && typeof option.delta === 'number') {
            modifierPrice += option.delta;
          }
        });
      } else if (modifier && typeof modifier.delta === 'number') {
        modifierPrice += modifier.delta;
      }
    });

    return (basePrice + modifierPrice) * quantity;
  };

  const handleModifierChange = (groupId, option, isMultiple = false) => {
    setSelectedModifiers(prev => {
      const newModifiers = { ...prev };
      
      if (isMultiple) {
        if (!newModifiers[groupId]) {
          newModifiers[groupId] = [];
        }
        
        const existingIndex = newModifiers[groupId].findIndex(item => item.id === option.id);
        if (existingIndex >= 0) {
          newModifiers[groupId].splice(existingIndex, 1);
        } else {
          newModifiers[groupId].push(option);
        }
      } else {
        newModifiers[groupId] = option;
      }
      
      return newModifiers;
    });
  };

  const handleAddToCart = async () => {
    try {
      console.log('Adding to cart - selectedModifiers:', selectedModifiers);
      console.log('Adding to cart - product:', product);
      
      // Convert modifiers to the format expected by the API
      const variations = [];
      const addons = [];
      const addonQtys = [];

      // Group variations by variation name
      const variationGroups = {};
      
      Object.entries(selectedModifiers).forEach(([groupId, modifier]) => {
        console.log('Processing group:', groupId, 'modifier:', modifier);
        
        if (groupId.startsWith('variation_')) {
          // This is a variation group
          const variationIndex = parseInt(groupId.replace('variation_', ''));
          const variation = product.variations[variationIndex];
          
          if (!variationGroups[variation.name]) {
            variationGroups[variation.name] = {
              name: variation.name,
              values: { label: [] }
            };
          }
          
          if (Array.isArray(modifier)) {
            // Multiple selection
            modifier.forEach(option => {
              if (option && option.label) {
                variationGroups[variation.name].values.label.push(option.label);
              }
            });
          } else if (modifier && modifier.label) {
            // Single selection
            variationGroups[variation.name].values.label.push(modifier.label);
          }
        } else if (groupId === 'addons') {
          // Handle add-ons
          if (Array.isArray(modifier)) {
            modifier.forEach(option => {
              if (option && option.id) {
                addons.push(option.id);
                addonQtys.push(option.quantity || 1);
              }
            });
          } else if (modifier && modifier.id) {
            addons.push(modifier.id);
            addonQtys.push(modifier.quantity || 1);
          }
        }
      });
      
      // Convert variation groups to array
      Object.values(variationGroups).forEach(variation => {
        variations.push(variation);
      });

      console.log('Final variations:', variations);
      console.log('Final addons:', addons);
      console.log('Final addonQtys:', addonQtys);

      if (isEditMode) {
        // Update existing cart item by removing old and adding new
        const oldItemKey = cartService.generateItemKey(editingItem.product_id, editingItem.variations || [], editingItem.add_ons || []);
        
        // Remove the old item
        const removeResult = await removeFromCart(oldItemKey);
        
        if (removeResult.success) {
          // Add the updated item
          const addResult = await addToCart(product.id, quantity, variations, addons, addonQtys);
          
          if (addResult.success) {
            // Clear editing state and close modal
            dispatch({ type: 'SET_EDITING_CART_ITEM', payload: null });
            onClose();
          } else {
            alert(addResult.message || 'Failed to update item in cart');
          }
        } else {
          alert(removeResult.message || 'Failed to remove old item from cart');
        }
      } else {
        // Add new item to cart
        const result = await addToCart(product.id, quantity, variations, addons, addonQtys);
        
        if (result.success) {
          onClose();
        } else {
          alert(result.message || 'Failed to add item to cart');
        }
      }
    } catch (error) {
      console.error('Error adding to cart:', error);
      alert('Failed to add item to cart');
    }
  };

  const isModifierValid = (group) => {
    if (!group.required) return true;
    
    const selected = selectedModifiers[group.id];
    if (group.type === 'single') {
      return selected && selected.id;
    } else {
      return Array.isArray(selected) && selected.length >= (group.min || 0) && selected.length <= (group.max || Infinity);
    }
  };

  const allModifiersValid = modifierGroups.every(isModifierValid);

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content" onClick={e => e.stopPropagation()}>
        <div className="p-6">
          {/* Header */}
          <div className="flex items-start justify-between mb-6">
            <div className="flex-1">
              <h2 className="text-2xl font-bold text-secondary mb-2">{product.name}</h2>
              <p className="text-gray-600">{product.description}</p>
            </div>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 text-2xl ml-4"
            >
              ×
            </button>
          </div>

          {/* Product Image */}
          <div className="aspect-video mb-6 overflow-hidden rounded-lg">
            <img
              src={product.image || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop'}
              alt={product.name}
              className="w-full h-full object-cover"
            />
          </div>

          {/* Modifiers */}
          {modifierGroups.map(group => (
            <div key={group.id} className="mb-6">
              <h3 className="text-lg font-semibold text-secondary mb-3">
                {group.name}
                {group.required && <span className="text-red-500 ml-1">*</span>}
                {group.type === 'multi' && (
                  <span className="text-sm text-gray-500 ml-2">
                    (Select {group.min || 0}-{group.max || 'unlimited'})
                  </span>
                )}
              </h3>
              
              <div className="space-y-2">
                {group.options.map(option => (
                  <label
                    key={option.id}
                    className={`flex items-center justify-between p-3 border rounded-lg cursor-pointer transition-colors ${
                      group.type === 'single'
                        ? selectedModifiers[group.id]?.id === option.id
                          ? 'border-primary bg-primary/10'
                          : 'border-gray-200 hover:border-gray-300'
                        : Array.isArray(selectedModifiers[group.id]) && selectedModifiers[group.id].some(item => item.id === option.id)
                          ? 'border-primary bg-primary/10'
                          : 'border-gray-200 hover:border-gray-300'
                    }`}
                  >
                    <div className="flex items-center">
                      <input
                        type={group.type === 'single' ? 'radio' : 'checkbox'}
                        name={group.id}
                        checked={
                          group.type === 'single'
                            ? selectedModifiers[group.id]?.id === option.id
                            : Array.isArray(selectedModifiers[group.id]) && selectedModifiers[group.id].some(item => item.id === option.id)
                        }
                        onChange={() => handleModifierChange(group.id, option, group.type === 'multi')}
                        className="mr-3"
                      />
                      <span className="text-gray-700">{option.label}</span>
                    </div>
                    {option.delta && option.delta > 0 && (
                      <span className="text-primary font-semibold">
                        +${option.delta.toFixed(2)}
                      </span>
                    )}
                  </label>
                ))}
              </div>
            </div>
          ))}

          {/* Quantity and Price */}
          <div className="flex items-center justify-between mb-6">
            <div className="flex items-center space-x-4">
              <label className="text-lg font-semibold text-secondary">Quantity:</label>
              <div className="flex items-center space-x-2">
                <button
                  onClick={() => setQuantity(Math.max(1, quantity - 1))}
                  className="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                >
                  -
                </button>
                <span className="text-lg font-semibold w-8 text-center">{quantity}</span>
                <button
                  onClick={() => setQuantity(quantity + 1)}
                  className="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                >
                  +
                </button>
              </div>
            </div>
            
            <div className="text-right">
              <div className="text-2xl font-bold text-primary">
                ${calculatePrice().toFixed(2)}
              </div>
            </div>
          </div>

          {/* Add to Cart Button */}
          <button
            onClick={handleAddToCart}
            disabled={!allModifiersValid}
            className="btn-primary w-full py-4 text-lg ripple disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isEditMode ? 'Update Item' : 'Add to Cart'} - ${calculatePrice().toFixed(2)}
          </button>
        </div>
      </div>
    </div>
  );
};

export default ProductModal;
