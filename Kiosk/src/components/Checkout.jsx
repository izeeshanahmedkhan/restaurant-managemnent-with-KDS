import { useState } from 'react';
import { useApp } from '../context/AppContext';

const Checkout = () => {
  const { state, dispatch } = useApp();
  const [paymentMethod, setPaymentMethod] = useState('');
  const [isProcessing, setIsProcessing] = useState(false);

  const getItemPrice = (item) => {
    const basePrice = item.price;
    const modifierPrice = item.modifiers?.reduce((total, mod) => total + mod.delta, 0) || 0;
    return basePrice + modifierPrice;
  };

  const getTotalPrice = () => {
    return state.cart.reduce((total, item) => {
      return total + getItemPrice(item) * item.quantity;
    }, 0);
  };

  const getTotalItems = () => {
    return state.cart.reduce((total, item) => total + item.quantity, 0);
  };


  const handlePaymentMethodChange = (method) => {
    setPaymentMethod(method);
  };

  const handleConfirmOrder = async () => {
    if (!paymentMethod) {
      alert('Please select a payment method');
      return;
    }

    setIsProcessing(true);

    // Simulate order processing
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Generate order number
    const orderNumber = Math.floor(Math.random() * 900000) + 100000;

    // Update state
    dispatch({ type: 'SET_ORDER_NUMBER', payload: orderNumber });
    dispatch({ type: 'SET_PAYMENT_METHOD', payload: paymentMethod });
    dispatch({ type: 'SET_SCREEN', payload: 'complete' });

    setIsProcessing(false);
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
          {/* Order Summary - Takes 2 columns on large screens */}
          <div className="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
            <div className="p-4 border-b border-gray-200">
              <h2 className="text-lg font-semibold text-gray-800">Order Summary</h2>
            </div>
            
            <div className="p-4">
              <div className="space-y-3">
                {state.cart.map((item, index) => (
                  <div key={`${item.id}-${index}`} className="flex items-center space-x-3 py-2">
                    <img
                      src={item.image}
                      alt={item.name}
                      className="w-10 h-10 object-cover rounded-md flex-shrink-0"
                      onError={(e) => {
                        e.target.src = 'https://via.placeholder.com/40x40?text=No+Image';
                      }}
                    />
                    <div className="flex-1 min-w-0">
                      <h3 className="font-medium text-gray-800 text-sm truncate">{item.name}</h3>
                      {item.modifiers && item.modifiers.length > 0 && (
                        <div className="text-xs text-gray-500 mt-1">
                          {item.modifiers.map((mod, modIndex) => (
                            <span key={modIndex}>
                              {mod.optionLabel}
                              {mod.delta > 0 && ` (+$${mod.delta.toFixed(2)})`}
                              {modIndex < item.modifiers.length - 1 && ', '}
                            </span>
                          ))}
                        </div>
                      )}
                    </div>
                    <div className="text-right flex-shrink-0">
                      <div className="font-semibold text-gray-800 text-sm">
                        ${(getItemPrice(item) * item.quantity).toFixed(2)}
                      </div>
                      <div className="text-xs text-gray-500">×{item.quantity}</div>
                    </div>
                  </div>
                ))}
              </div>

              <div className="border-t border-gray-200 mt-4 pt-4">
                <div className="flex justify-between items-center">
                  <span className="font-medium text-gray-600 text-sm">
                    Total ({getTotalItems()} items)
                  </span>
                  <span className="text-lg font-bold text-primary">
                    ${getTotalPrice().toFixed(2)}
                  </span>
                </div>
              </div>
            </div>
          </div>

          {/* Payment Method - Takes 1 column on large screens */}
          <div className="bg-white rounded-lg shadow-sm border border-gray-200">
            <div className="p-4 border-b border-gray-200">
              <h2 className="text-lg font-semibold text-gray-800">Payment</h2>
            </div>
            
            <div className="p-4">
              <div className="space-y-2 mb-4">
                <label className={`flex items-center p-3 rounded-md border cursor-pointer transition-all duration-200 ${
                  paymentMethod === 'cash' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'
                }`}>
                  <input
                    type="radio"
                    name="payment"
                    value="cash"
                    checked={paymentMethod === 'cash'}
                    onChange={(e) => handlePaymentMethodChange(e.target.value)}
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
                  paymentMethod === 'card' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'
                }`}>
                  <input
                    type="radio"
                    name="payment"
                    value="card"
                    checked={paymentMethod === 'card'}
                    onChange={(e) => handlePaymentMethodChange(e.target.value)}
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

              {/* Confirm Order Button */}
              <button
                onClick={handleConfirmOrder}
                disabled={!paymentMethod || isProcessing}
                className="btn-primary w-full py-3 text-sm font-medium ripple disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {isProcessing ? (
                  <div className="flex items-center justify-center">
                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Processing...
                  </div>
                ) : (
                  'Confirm Order'
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
