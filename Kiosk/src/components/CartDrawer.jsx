import { useApp } from '../context/AppContext';

const CartDrawer = ({ onClose, onCheckout }) => {
  const { state, dispatch } = useApp();

  const updateQuantity = (item, newQuantity) => {
    if (newQuantity <= 0) {
      dispatch({
        type: 'REMOVE_FROM_CART',
        payload: {
          id: item.id,
          modifiers: item.modifiers
        }
      });
    } else {
      dispatch({
        type: 'UPDATE_CART_ITEM_QUANTITY',
        payload: {
          id: item.id,
          modifiers: item.modifiers,
          quantity: newQuantity
        }
      });
    }
  };

  const removeItem = (item) => {
    dispatch({
      type: 'REMOVE_FROM_CART',
      payload: {
        id: item.id,
        modifiers: item.modifiers
      }
    });
  };

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

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-end">
      <div className="bg-white w-full max-w-md h-full shadow-2xl slide-up">
        {/* Header */}
        <div className="p-6 border-b border-gray-200">
          <div className="flex items-center justify-between">
            <h2 className="text-2xl font-bold text-secondary">Your Order</h2>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600 text-2xl"
            >
              ×
            </button>
          </div>
          <p className="text-gray-600 mt-1">{getTotalItems()} item(s) in cart</p>
        </div>

        {/* Cart Items */}
        <div className="flex-1 overflow-y-auto p-6">
          {state.cart.length === 0 ? (
            <div className="text-center py-12">
              <div className="text-6xl mb-4">🛒</div>
              <h3 className="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h3>
              <p className="text-gray-500">Add some delicious items to get started!</p>
            </div>
          ) : (
            <div className="space-y-4">
              {state.cart.map((item, index) => (
                <div key={`${item.id}-${index}`} className="bg-gray-50 rounded-lg p-4">
                  <div className="flex items-start space-x-3">
                    <img
                      src={item.image}
                      alt={item.name}
                      className="w-16 h-16 object-cover rounded-lg"
                      onError={(e) => {
                        e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2YzZjRmNiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM2NjY2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBJbWFnZTwvdGV4dD48L3N2Zz4=';
                      }}
                    />
                    
                    <div className="flex-1 min-w-0">
                      <h3 className="font-semibold text-secondary mb-1">{item.name}</h3>
                      
                      {item.modifiers && item.modifiers.length > 0 && (
                        <div className="text-sm text-gray-600 mb-2">
                          {item.modifiers.map((mod, modIndex) => (
                            <div key={modIndex} className="flex items-center space-x-1">
                              <span>{mod.optionLabel}</span>
                              {mod.delta > 0 && (
                                <span className="text-primary">(+${mod.delta.toFixed(2)})</span>
                              )}
                            </div>
                          ))}
                        </div>
                      )}
                      
                      <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                          <button
                            onClick={() => updateQuantity(item, item.quantity - 1)}
                            className="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-lg font-bold"
                          >
                            -
                          </button>
                          <span className="w-8 text-center font-semibold">{item.quantity}</span>
                          <button
                            onClick={() => updateQuantity(item, item.quantity + 1)}
                            className="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-lg font-bold"
                          >
                            +
                          </button>
                        </div>
                        
                        <div className="text-right">
                          <div className="font-semibold text-primary">
                            ${(getItemPrice(item) * item.quantity).toFixed(2)}
                          </div>
                          <button
                            onClick={() => removeItem(item)}
                            className="text-red-500 hover:text-red-700 text-sm"
                          >
                            Remove
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Footer */}
        {state.cart.length > 0 && (
          <div className="p-6 border-t border-gray-200 bg-white">
            <div className="flex items-center justify-between mb-4">
              <span className="text-xl font-semibold text-secondary">Total:</span>
              <span className="text-2xl font-bold text-primary">
                ${getTotalPrice().toFixed(2)}
              </span>
            </div>
            
            <button
              onClick={onCheckout}
              className="btn-primary w-full py-4 text-lg ripple"
            >
              Proceed to Checkout
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default CartDrawer;
