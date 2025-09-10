import { useApp } from '../context/AppContext';
import { cartService } from '../services/cartService';

const CartDrawer = ({ onClose, onCheckout }) => {
  const { state, dispatch, updateCartItem, removeFromCart, startOver } = useApp();

  const calculateItemPrice = (item) => {
    let basePrice = item.price || 0;
    let modifierPrice = 0;

    Object.values(item.modifiers || {}).forEach(modifier => {
      if (Array.isArray(modifier)) {
        modifier.forEach(option => {
          modifierPrice += option.delta || 0;
        });
      } else if (modifier && modifier.delta) {
        modifierPrice += modifier.delta;
      }
    });

    return (basePrice + modifierPrice) * item.quantity;
  };

  const calculateTotal = () => {
    return state.cart.total || 0;
  };

  const handleQuantityChange = async (item, newQuantity) => {
    const itemKey = cartService.generateItemKey(item.product_id, item.variations || [], item.add_ons || []);
    
    if (newQuantity <= 0) {
      await removeFromCart(itemKey);
    } else {
      await updateCartItem(itemKey, newQuantity);
    }
  };

  const handleRemoveItem = async (item) => {
    const itemKey = cartService.generateItemKey(item.product_id, item.variations || [], item.add_ons || []);
    await removeFromCart(itemKey);
  };

  const handleEditItem = async (item) => {
    try {
      // Close the cart drawer
      onClose();
      
      // Fetch the detailed product data
      const { menuService } = await import('../services/menuService');
      const detailedProduct = await menuService.getProduct(item.product_id);
      
      if (detailedProduct) {
        // Set the product and editing item in the context
        dispatch({ type: 'SET_SELECTED_PRODUCT', payload: detailedProduct });
        dispatch({ type: 'SET_EDITING_CART_ITEM', payload: item });
        dispatch({ type: 'TOGGLE_PRODUCT_MODAL', payload: true });
      } else {
        alert('Failed to load product details for editing');
      }
    } catch (error) {
      console.error('Error opening edit modal:', error);
      alert('Failed to open edit modal');
    }
  };

  const handleStartOver = async () => {
    await startOver();
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-end" onClick={onClose}>
      <div className="bg-white w-full max-w-md h-full overflow-y-auto shadow-lg" onClick={(e) => e.stopPropagation()}>
        {/* Compact Header */}
        <div className="bg-white border-b border-gray-200 p-4">
          <div className="flex items-center justify-between">
            <h2 className="text-lg font-bold text-gray-800">Your Cart</h2>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 text-xl"
            >
              ×
            </button>
          </div>
        </div>

        <div className="p-4">

          {/* Cart Items */}
          {state.cart.items.length === 0 ? (
            <div className="text-center py-8">
              <div className="text-4xl mb-3">🛒</div>
              <h3 className="text-lg font-semibold text-gray-600 mb-1">Your cart is empty</h3>
              <p className="text-sm text-gray-500">Add some delicious items to get started!</p>
            </div>
          ) : (
            <div className="space-y-3 mb-4">
              {state.cart.items.map((item, index) => (
                <div key={index} className="bg-gray-50 rounded-md p-3 border border-gray-200">
                  <div className="flex items-start space-x-3">
                    <img
                      src={item.product_image || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=60&h=60&fit=crop'}
                      alt={item.product_name}
                      className="w-12 h-12 object-cover rounded-md flex-shrink-0"
                    />
                    
                    <div className="flex-1 min-w-0">
                      <h3 className="font-medium text-gray-800 text-sm truncate">{item.product_name}</h3>
                      
                      {/* Compact Base Price */}
                      <div className="text-xs text-gray-600 mt-1">
                        <span className="bg-green-100 text-green-700 px-1.5 py-0.5 rounded text-xs">
                          ${parseFloat(item.base_price || 0).toFixed(2)}
                        </span>
                      </div>
                      
                      {/* Compact Variations */}
                      {item.variation_details && item.variation_details.length > 0 && (
                        <div className="mt-1">
                          <div className="flex flex-wrap gap-1">
                            {item.variation_details.map((variationDetail, idx) => (
                              variationDetail.selected_options && variationDetail.selected_options.length > 0 && (
                                variationDetail.selected_options.map((option, optIdx) => (
                                  <span key={`${idx}-${optIdx}`} className="bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded text-xs">
                                    {option.label}
                                    {option.price > 0 && ` (+$${parseFloat(option.price).toFixed(2)})`}
                                  </span>
                                ))
                              )
                            ))}
                          </div>
                          {parseFloat(item.variation_price || 0) > 0 && (
                            <div className="text-xs text-green-600 mt-1 font-medium">
                              +${parseFloat(item.variation_price || 0).toFixed(2)}
                            </div>
                          )}
                        </div>
                      )}
                      
                      {/* Compact Add-ons */}
                      {item.addon_details && item.addon_details.length > 0 && (
                        <div className="mt-1">
                          <div className="flex flex-wrap gap-1">
                            {item.addon_details.map((addon, idx) => (
                              <span key={idx} className="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded text-xs">
                                {addon.name}
                                {parseFloat(addon.price || 0) > 0 && ` (+$${parseFloat(addon.price || 0).toFixed(2)})`}
                                {addon.quantity > 1 && ` x${addon.quantity}`}
                              </span>
                            ))}
                          </div>
                          {item.addon_price && parseFloat(item.addon_price) > 0 && (
                            <div className="text-xs text-green-600 mt-1 font-medium">
                              +${parseFloat(item.addon_price).toFixed(2)}
                            </div>
                          )}
                        </div>
                      )}
                      
                      {/* Compact Controls */}
                      <div className="flex items-center justify-between mt-2">
                        <div className="flex items-center space-x-1">
                          <button
                            onClick={() => handleQuantityChange(item, item.quantity - 1)}
                            className="w-5 h-5 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-xs"
                          >
                            -
                          </button>
                          <span className="font-medium text-sm px-2">{item.quantity}</span>
                          <button
                            onClick={() => handleQuantityChange(item, item.quantity + 1)}
                            className="w-5 h-5 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-xs"
                          >
                            +
                          </button>
                        </div>
                        
                        <div className="flex flex-col items-end space-y-1">
                          <div className="text-xs text-gray-600">
                            Single: ${(calculateItemPrice(item) / item.quantity).toFixed(2)}
                          </div>
                          <span className="font-semibold text-primary text-sm">
                            Total: ${calculateItemPrice(item).toFixed(2)}
                          </span>
                          <div className="flex space-x-1">
                            <button
                              onClick={() => handleEditItem(item)}
                              className="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors"
                            >
                              Edit
                            </button>
                            <button
                              onClick={() => handleRemoveItem(item)}
                              className="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600 transition-colors"
                            >
                              Remove
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* Cart Expiration Warning */}
          {state.cartTimeRemaining > 0 && state.cartTimeRemaining <= 5 && (
            <div className="bg-yellow-100 border border-yellow-400 text-yellow-700 px-3 py-2 rounded text-sm mb-3">
              <div className="flex items-center">
                <span className="text-xs">
                  ⚠️ Cart will expire in {state.cartTimeRemaining} seconds
                </span>
              </div>
            </div>
          )}

          {/* Start Over Button */}
          {state.cart.items.length > 0 && (
            <div className="mb-3">
              <button
                onClick={handleStartOver}
                className="btn-secondary w-full py-2 text-xs ripple"
              >
                Start Over
              </button>
            </div>
          )}

          {/* Total and Checkout */}
          {state.cart.items.length > 0 && (
            <div className="border-t border-gray-200 pt-4">
              <div className="flex justify-between items-center mb-3">
                <span className="text-lg font-semibold text-gray-800">Total:</span>
                <span className="text-xl font-bold text-primary">
                  ${calculateTotal().toFixed(2)}
                </span>
              </div>
              
              <button
                onClick={onCheckout}
                className="btn-primary w-full py-3 text-sm ripple"
              >
                Proceed to Checkout
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default CartDrawer;
