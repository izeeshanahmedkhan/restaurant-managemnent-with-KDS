import { useRef } from 'react';
import { useApp } from '../context/AppContext';
import SignOutButton from './SignOutButton';
import SignOutModal from './SignOutModal';

const OrderComplete = () => {
  const { state, dispatch } = useApp();
  const receiptRef = useRef(null);

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

  const handlePrintReceipt = () => {
    if (receiptRef.current) {
      window.print();
    }
  };

  const handleStartNewOrder = () => {
    dispatch({ type: 'RESET_ORDER' });
    dispatch({ type: 'SET_SCREEN', payload: 'welcome' });
  };

  const formatTime = () => {
    const now = new Date();
    return now.toLocaleString();
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-2xl text-center page-transition">
        {/* Success Icon */}
        <div className="text-8xl mb-6">✅</div>
        
        <h1 className="text-4xl font-bold text-secondary mb-4">
          Order Complete!
        </h1>
        
        <div className="bg-primary/10 rounded-xl p-6 mb-6">
          <div className="text-6xl font-bold text-primary mb-2">
            #{state.orderNumber}
          </div>
          <p className="text-lg text-gray-600">Your order number</p>
        </div>

        <div className="text-left bg-gray-50 rounded-xl p-6 mb-6">
          <h2 className="text-xl font-bold text-secondary mb-4">Order Summary</h2>
          
          <div className="space-y-3 mb-4">
            {state.cart.map((item, index) => (
              <div key={`${item.id}-${index}`} className="flex justify-between items-center py-2 border-b border-gray-200">
                <div className="flex-1">
                  <div className="font-semibold text-secondary">{item.name}</div>
                  {item.modifiers && item.modifiers.length > 0 && (
                    <div className="text-sm text-gray-600">
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
                <div className="text-right">
                  <div className="font-semibold text-primary">
                    ${(getItemPrice(item) * item.quantity).toFixed(2)}
                  </div>
                  <div className="text-sm text-gray-500">Qty: {item.quantity}</div>
                </div>
              </div>
            ))}
          </div>

          <div className="border-t border-gray-300 pt-3">
            <div className="flex justify-between items-center text-lg font-bold text-secondary">
              <span>Total ({getTotalItems()} items):</span>
              <span className="text-primary">${getTotalPrice().toFixed(2)}</span>
            </div>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row gap-4 justify-center">
          <button
            onClick={handlePrintReceipt}
            className="btn-secondary px-8 py-3 text-lg ripple"
          >
            🖨️ Print Receipt
          </button>
          
          <button
            onClick={handleStartNewOrder}
            className="btn-primary px-8 py-3 text-lg ripple"
          >
            Start New Order
          </button>
        </div>

        <div className="mt-6 text-sm text-gray-500">
          <p>Order placed at: {formatTime()}</p>
          <p>Payment method: {state.paymentMethod === 'cash' ? 'Cash' : 'Card'}</p>
        </div>
      </div>

      {/* Hidden Receipt for Printing */}
      <div ref={receiptRef} className="receipt hidden print:block">
        <div className="max-w-sm mx-auto p-4 text-black">
          <div className="text-center mb-4">
            <h1 className="text-2xl font-bold">Restaurant Kiosk</h1>
            <p className="text-sm">Order Receipt</p>
          </div>
          
          <div className="border-t border-b border-gray-400 py-2 mb-4">
            <div className="flex justify-between">
              <span>Order #:</span>
              <span className="font-bold">#{state.orderNumber}</span>
            </div>
            <div className="flex justify-between">
              <span>Date:</span>
              <span>{formatTime()}</span>
            </div>
            <div className="flex justify-between">
              <span>Payment:</span>
              <span>{state.paymentMethod === 'cash' ? 'Cash' : 'Card'}</span>
            </div>
          </div>

          <div className="mb-4">
            {state.cart.map((item, index) => (
              <div key={`${item.id}-${index}`} className="mb-2">
                <div className="flex justify-between">
                  <span className="font-semibold">{item.name} x{item.quantity}</span>
                  <span>${(getItemPrice(item) * item.quantity).toFixed(2)}</span>
                </div>
                {item.modifiers && item.modifiers.length > 0 && (
                  <div className="text-sm ml-4 text-gray-600">
                    {item.modifiers.map((mod, modIndex) => (
                      <div key={modIndex}>
                        + {mod.optionLabel}
                        {mod.delta > 0 && ` (+$${mod.delta.toFixed(2)})`}
                      </div>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </div>

          <div className="border-t border-gray-400 pt-2">
            <div className="flex justify-between text-lg font-bold">
              <span>Total:</span>
              <span>${getTotalPrice().toFixed(2)}</span>
            </div>
          </div>

          <div className="text-center mt-6 text-sm">
            <p>Thank you for your order!</p>
            <p>Please keep this receipt</p>
          </div>
        </div>
      </div>
      
      <SignOutButton />
      <SignOutModal 
        isOpen={state.isSignOutModalOpen} 
        onClose={() => dispatch({ type: 'TOGGLE_SIGN_OUT_MODAL', payload: false })} 
      />
    </div>
  );
};

export default OrderComplete;
