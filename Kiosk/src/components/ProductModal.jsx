import { useState, useMemo } from 'react';
import { useApp } from '../context/AppContext';

const ProductModal = ({ product, onClose }) => {
  const { dispatch } = useApp();
  const [selectedModifiers, setSelectedModifiers] = useState({});

  const totalPrice = useMemo(() => {
    const basePrice = product.price;
    let modifierPrice = 0;

    Object.entries(selectedModifiers).forEach(([groupId, selections]) => {
      if (Array.isArray(selections)) {
        // Multiple selection
        selections.forEach(optionId => {
          const group = product.modifierGroups.find(g => g.id === groupId);
          const option = group?.options.find(o => o.id === optionId);
          if (option) modifierPrice += option.delta;
        });
      } else if (selections) {
        // Single selection
        const group = product.modifierGroups.find(g => g.id === groupId);
        const option = group?.options.find(o => o.id === selections);
        if (option) modifierPrice += option.delta;
      }
    });

    return basePrice + modifierPrice;
  }, [product.price, selectedModifiers, product.modifierGroups]);

  const handleModifierChange = (groupId, optionId, isMultiple = false) => {
    setSelectedModifiers(prev => {
      const newModifiers = { ...prev };
      
      if (isMultiple) {
        const currentSelections = newModifiers[groupId] || [];
        if (currentSelections.includes(optionId)) {
          newModifiers[groupId] = currentSelections.filter(id => id !== optionId);
        } else {
          newModifiers[groupId] = [...currentSelections, optionId];
        }
      } else {
        newModifiers[groupId] = optionId;
      }
      
      return newModifiers;
    });
  };

  const handleAddToCart = () => {
    const modifiers = [];
    
    Object.entries(selectedModifiers).forEach(([groupId, selections]) => {
      const group = product.modifierGroups.find(g => g.id === groupId);
      if (!group) return;
      
      if (Array.isArray(selections)) {
        selections.forEach(optionId => {
          const option = group.options.find(o => o.id === optionId);
          if (option) {
            modifiers.push({
              groupId,
              groupName: group.name,
              optionId,
              optionLabel: option.label,
              delta: option.delta
            });
          }
        });
      } else if (selections) {
        const option = group.options.find(o => o.id === selections);
        if (option) {
          modifiers.push({
            groupId,
            groupName: group.name,
            optionId: selections,
            optionLabel: option.label,
            delta: option.delta
          });
        }
      }
    });

    dispatch({
      type: 'ADD_TO_CART',
      payload: {
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image,
        modifiers
      }
    });

    onClose();
  };

  const isModifierValid = (group) => {
    if (!group.required) return true;
    
    const selections = selectedModifiers[group.id];
    if (group.type === 'single') {
      return !!selections;
    } else {
      const count = Array.isArray(selections) ? selections.length : 0;
      return count >= (group.min || 0) && count <= (group.max || Infinity);
    }
  };

  const allRequiredModifiersValid = product.modifierGroups.every(group => isModifierValid(group));

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content scale-in" onClick={e => e.stopPropagation()}>
        <div className="p-6">
          {/* Header */}
          <div className="flex items-start justify-between mb-6">
            <div className="flex-1">
              <h2 className="text-3xl font-bold text-secondary mb-2">{product.name}</h2>
              <p className="text-gray-600 text-lg">{product.description}</p>
            </div>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 text-3xl ml-4"
            >
              ×
            </button>
          </div>

          {/* Product Image */}
          <div className="mb-6">
            <img
              src={product.image}
              alt={product.name}
              className="w-full h-64 object-cover rounded-xl"
              onError={(e) => {
                e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzY2NjY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
              }}
            />
          </div>

          {/* Modifier Groups */}
          <div className="space-y-6 mb-8">
            {product.modifierGroups.map(group => (
              <div key={group.id} className="border border-gray-200 rounded-lg p-4">
                <h3 className="text-lg font-semibold text-secondary mb-3">
                  {group.name}
                  {group.required && <span className="text-red-500 ml-1">*</span>}
                  {group.min > 0 && (
                    <span className="text-sm text-gray-500 ml-2">
                      (Select {group.min}-{group.max || 'unlimited'})
                    </span>
                  )}
                </h3>
                
                <div className="space-y-3">
                  {group.options.map(option => (
                    <label
                      key={option.id}
                      className={`flex items-center justify-between p-3 rounded-lg border-2 cursor-pointer transition-all duration-200 ${
                        (group.type === 'single' && selectedModifiers[group.id] === option.id) ||
                        (group.type === 'multiple' && selectedModifiers[group.id]?.includes(option.id))
                          ? 'border-primary bg-primary/10'
                          : 'border-gray-200 hover:border-gray-300'
                      }`}
                    >
                      <div className="flex items-center space-x-3">
                        <input
                          type={group.type === 'single' ? 'radio' : 'checkbox'}
                          name={group.id}
                          value={option.id}
                          checked={
                            group.type === 'single'
                              ? selectedModifiers[group.id] === option.id
                              : selectedModifiers[group.id]?.includes(option.id) || false
                          }
                          onChange={() => handleModifierChange(group.id, option.id, group.type === 'multiple')}
                          className="w-5 h-5 text-primary focus:ring-primary"
                        />
                        <span className="font-medium">{option.label}</span>
                      </div>
                      {option.delta > 0 && (
                        <span className="text-primary font-semibold">
                          +${option.delta.toFixed(2)}
                        </span>
                      )}
                    </label>
                  ))}
                </div>
                
                {!isModifierValid(group) && (
                  <p className="text-red-500 text-sm mt-2">
                    {group.required ? 'This selection is required' : 'Invalid selection'}
                  </p>
                )}
              </div>
            ))}
          </div>

          {/* Footer */}
          <div className="flex items-center justify-between pt-6 border-t border-gray-200">
            <div className="text-3xl font-bold text-primary">
              ${totalPrice.toFixed(2)}
            </div>
            
            <button
              onClick={handleAddToCart}
              disabled={!allRequiredModifiersValid}
              className="btn-primary px-8 py-3 text-lg ripple disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductModal;
