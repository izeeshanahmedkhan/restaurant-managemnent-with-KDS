import { useState } from 'react';
import { useApp } from '../context/AppContext';
import { orderService } from '../services/orderService';
import { cartService } from '../services/cartService';

const Checkout = () => {
  const { state, dispatch, removeFromCart, updateCartItem } = useApp();
  const [isProcessing, setIsProcessing] = useState(false);
  const [error, setError] = useState('');

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
    if (newQuantity < 1) return;
    
    try {
      const itemKey = cartService.generateItemKey(item.product_id, item.variations || [], item.add_ons || []);
      const result = await updateCartItem(itemKey, newQuantity);
      
      if (!result.success) {
        alert(result.message || 'Failed to update quantity');
      }
    } catch (error) {
      console.error('Error updating quantity:', error);
      alert('Failed to update quantity');
    }
  };

  const handleEditItem = async (item) => {
    console.log('Edit button clicked for item:', item);
    try {
      // Fetch the detailed product data
      const { menuService } = await import('../services/menuService');
      console.log('Fetching product details for ID:', item.product_id);
      const detailedProduct = await menuService.getProduct(item.product_id);
      console.log('Product details fetched:', detailedProduct);
      
      if (detailedProduct) {
        // Set the product and editing item in the context
        console.log('Setting product and editing item in context');
        dispatch({ type: 'SET_SELECTED_PRODUCT', payload: detailedProduct });
        dispatch({ type: 'SET_EDITING_CART_ITEM', payload: item });
        dispatch({ type: 'TOGGLE_PRODUCT_MODAL', payload: true });
        console.log('Product modal should be opening now');
      } else {
        console.log('Failed to load product details');
        alert('Failed to load product details for editing');
      }
    } catch (error) {
      console.error('Error opening edit modal:', error);
      alert('Failed to open edit modal');
    }
  };

  const handleRemoveItem = async (item) => {
    try {
      // Generate the correct item key for removal
      const itemKey = cartService.generateItemKey(item.product_id, item.variations || [], item.add_ons || []);
      await removeFromCart(itemKey);
    } catch (error) {
      console.error('Error removing item:', error);
      setError('Failed to remove item from cart');
    }
  };

  const handlePlaceOrder = async () => {
    if (state.cart.items.length === 0) return;

    setIsProcessing(true);
    setError('');

    try {
      const orderData = {
        payment_method: state.paymentMethod,
        order_note: ''
      };

      const result = await orderService.createOrder(orderData);
      
      if (result.success) {
        dispatch({ type: 'SET_ORDER_NUMBER', payload: result.data.order_number });
        dispatch({ type: 'SET_ORDER_DATA', payload: result.data });
        dispatch({ type: 'CLEAR_CART' });
        dispatch({ type: 'SET_SCREEN', payload: 'complete' });
      } else {
        setError(result.message || 'Failed to place order');
      }
    } catch (err) {
      setError('An error occurred. Please try again.');
    } finally {
      setIsProcessing(false);
    }
  };

  const handleBack = () => {
    dispatch({ type: 'SET_SCREEN', payload: 'menu' });
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Compact Header */}
      <div className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-3xl mx-auto px-4 py-3">
          <div className="flex items-center justify-between">
            <button
              onClick={handleBack}
              className="flex items-center space-x-2 text-gray-600 hover:text-gray-800 transition-colors"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
              <span className="font-medium">Back</span>
            </button>
            <h1 className="text-lg font-bold text-gray-800">Checkout</h1>
            <div className="w-16"></div>
          </div>
        </div>
      </div>

      <div className="max-w-3xl mx-auto p-4">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
          {/* Order Summary - Takes 2 columns */}
          <div className="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
            <div className="p-4 border-b border-gray-200">
              <h2 className="text-lg font-semibold text-gray-800">Order Summary</h2>
            </div>
            <div className="p-4">
              <div className="space-y-3">
                {state.cart.items.map((item, index) => (
                  <div key={index} className="bg-gray-50 rounded-md p-3 border border-gray-200">
                    {/* Compact header with image and basic info */}
                    <div className="flex items-start space-x-3">
                      <img
                        src={item.product_image || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=60&h=60&fit=crop'}
                    alt={item.product_name}
                        className="w-12 h-12 object-cover rounded-md flex-shrink-0"
                  />
                  
                      <div className="flex-1 min-w-0">
                        <div className="flex items-start justify-between">
                  <div className="flex-1">
                            <h3 className="font-medium text-gray-800 text-sm truncate">{item.product_name}</h3>
                            <div className="flex items-center space-x-2 mt-1">
                              {/* Quantity Controls */}
                              <div className="flex items-center space-x-1">
                                <button
                                  onClick={() => handleQuantityChange(item, item.quantity - 1)}
                                  className="w-5 h-5 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-xs font-bold"
                                  disabled={item.quantity <= 1}
                                >
                                  -
                                </button>
                                <span className="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs min-w-[2rem] text-center">
                                  {item.quantity}
                                </span>
                                <button
                                  onClick={() => handleQuantityChange(item, item.quantity + 1)}
                                  className="w-5 h-5 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-xs font-bold"
                                >
                                  +
                                </button>
                              </div>
                              <span className="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">
                                ${parseFloat(item.base_price || 0).toFixed(2)}
                            </span>
                          </div>
                        </div>
                        
                          {/* Compact action buttons */}
                          <div className="flex space-x-1 ml-2">
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

                    {/* Compact Variations */}
                  {(item.variation_details && item.variation_details.length > 0) || (item.variations && item.variations.length > 0) ? (
                      <div className="mt-2">
                        <div className="bg-white rounded p-2 border border-gray-200">
                          <div className="text-xs font-medium text-gray-600 mb-1">Variations:</div>
                        {item.variation_details ? (
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
                        ) : (
                      <div className="text-xs text-gray-500">
                              {JSON.stringify(item.variations)}
                            </div>
                          )}
                          {parseFloat(item.variation_price || 0) > 0 && (
                            <div className="text-xs text-green-600 mt-1 font-medium">
                              +${parseFloat(item.variation_price || 0).toFixed(2)}
                      </div>
                    )}
                      </div>
                    </div>
                  ) : null}

                    {/* Compact Add-ons */}
                  {(item.addon_details && item.addon_details.length > 0) || (item.add_ons && item.add_ons.length > 0) ? (
                      <div className="mt-2">
                        <div className="bg-white rounded p-2 border border-gray-200">
                          <div className="text-xs font-medium text-gray-600 mb-1">Add-ons:</div>
                        {item.addon_details ? (
                            <div className="flex flex-wrap gap-1">
                            {item.addon_details.map((addon, idx) => (
                                <span key={idx} className="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded text-xs">
                                  {addon.name}
                                  {parseFloat(addon.price || 0) > 0 && ` (+$${parseFloat(addon.price || 0).toFixed(2)})`}
                                  {addon.quantity > 1 && ` x${addon.quantity}`}
                                </span>
                            ))}
                          </div>
                        ) : (
                      <div className="text-xs text-gray-500">
                              {JSON.stringify(item.add_ons)}
                      </div>
                    )}
                        {item.addon_price && parseFloat(item.addon_price) > 0 && (
                            <div className="text-xs text-green-600 mt-1 font-medium">
                              +${parseFloat(item.addon_price).toFixed(2)}
                          </div>
                        )}
                      </div>
                  </div>
                  ) : null}

                    {/* Compact Price Summary */}
                    <div className="mt-2 bg-green-50 rounded p-2 border border-green-200">
                      <div className="flex justify-between items-center mb-1">
                        <span className="text-xs font-medium text-gray-600">Single Item:</span>
                        <span className="text-sm font-bold text-green-600">${parseFloat(item.price || 0).toFixed(2)}</span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-xs font-medium text-gray-600">Total ({item.quantity}x):</span>
                        <span className="text-sm font-bold text-green-700">${(parseFloat(item.price || 0) * item.quantity).toFixed(2)}</span>
                      </div>
                    </div>
                </div>
                ))}
            </div>

              <div className="border-t border-gray-200 mt-4 pt-4">
              <div className="flex justify-between items-center">
                  <span className="font-medium text-gray-600 text-sm">
                    Total ({state.cart.items.length} item{state.cart.items.length !== 1 ? 's' : ''})
                  </span>
                  <span className="text-lg font-bold text-primary">
                    ${calculateTotal().toFixed(2)}
                  </span>
                </div>
              </div>
            </div>
          </div>

          {/* Payment Section - Takes 1 column */}
          <div className="bg-white rounded-lg shadow-sm border border-gray-200">
            <div className="p-4 border-b border-gray-200">
              <h2 className="text-lg font-semibold text-gray-800">Payment</h2>
            </div>
            <div className="p-4">
              <div className="space-y-2 mb-4">
                <label className={`flex items-center p-3 rounded-md border cursor-pointer transition-all duration-200 ${
                  state.paymentMethod === 'cash' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'
                }`}>
                    <input
                      type="radio"
                      name="paymentMethod"
                      value="cash"
                      checked={state.paymentMethod === 'cash'}
                      onChange={(e) => dispatch({ type: 'SET_PAYMENT_METHOD', payload: e.target.value })}
                    className="w-4 h-4 text-primary focus:ring-primary"
                    />
                  <div className="ml-3 flex items-center space-x-2">
                    <span className="text-lg">💵</span>
                    <div>
                      <div className="font-medium text-gray-800 text-sm">Cash</div>
                      <div className="text-xs text-gray-500">Pay at counter</div>
                    </div>
                    </div>
                  </label>
                  
                <label className={`flex items-center p-3 rounded-md border cursor-pointer transition-all duration-200 ${
                  state.paymentMethod === 'card' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'
                }`}>
                    <input
                      type="radio"
                      name="paymentMethod"
                      value="card"
                      checked={state.paymentMethod === 'card'}
                      onChange={(e) => dispatch({ type: 'SET_PAYMENT_METHOD', payload: e.target.value })}
                    className="w-4 h-4 text-primary focus:ring-primary"
                    />
                  <div className="ml-3 flex items-center space-x-2">
                    <span className="text-lg">💳</span>
                    <div>
                      <div className="font-medium text-gray-800 text-sm">Card</div>
                      <div className="text-xs text-gray-500">Credit/Debit</div>
                    </div>
                    </div>
                  </label>
              </div>

              {error && (
                <div className="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded text-sm mb-4">
                  {error}
                </div>
              )}

              <button
                onClick={handlePlaceOrder}
                disabled={isProcessing || state.cart.items.length === 0 || !state.paymentMethod}
                className="btn-primary w-full py-3 text-sm font-medium ripple disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {isProcessing ? (
                  <div className="flex items-center justify-center">
                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Processing...
                  </div>
                ) : (
                  `Place Order - $${calculateTotal().toFixed(2)}`
                )}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Checkout;
