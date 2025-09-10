import { createContext, useContext, useReducer, useEffect } from 'react';
import { authService } from '../services/authService';
import { cartService } from '../services/cartService';

const AppContext = createContext();

const initialState = {
  currentScreen: 'signin', // signin, welcome, menu, checkout, complete
  user: null,
  cart: {
    items: [],
    total: 0,
    item_count: 0,
    expires_at: null
  },
  selectedProduct: null,
  editingCartItem: null, // Item being edited
  orderNumber: null,
  orderData: null, // Store complete order data for receipt preview
  searchQuery: '',
  selectedCategory: null,
  isProductModalOpen: false,
  isCartOpen: false,
  isSignOutModalOpen: false,
  paymentMethod: null,
  cartTimeRemaining: 0,
  isCartExpired: false
};

const appReducer = (state, action) => {
  switch (action.type) {
    case 'SET_SCREEN':
      return { ...state, currentScreen: action.payload };
    
    case 'SET_USER':
      return { ...state, user: action.payload };
    
    case 'SET_CART':
      return { ...state, cart: action.payload };
    
    case 'UPDATE_CART_TIME':
      return { 
        ...state, 
        cartTimeRemaining: action.payload.timeRemaining,
        isCartExpired: action.payload.isExpired
      };
    
    case 'CLEAR_CART':
      return { 
        ...state, 
        cart: { items: [], total: 0, item_count: 0, expires_at: null },
        cartTimeRemaining: 0,
        isCartExpired: false
      };
    
    case 'SET_SELECTED_PRODUCT':
      return { ...state, selectedProduct: action.payload };
    
    case 'SET_EDITING_CART_ITEM':
      return { ...state, editingCartItem: action.payload };
    
    case 'SET_ORDER_NUMBER':
      return { ...state, orderNumber: action.payload };
    
    case 'SET_ORDER_DATA':
      return { ...state, orderData: action.payload };
    
    case 'SET_SEARCH_QUERY':
      return { ...state, searchQuery: action.payload };
    
    case 'SET_SELECTED_CATEGORY':
      return { ...state, selectedCategory: action.payload };
    
    case 'TOGGLE_PRODUCT_MODAL':
      return { ...state, isProductModalOpen: action.payload };
    
    case 'TOGGLE_CART':
      return { ...state, isCartOpen: action.payload };
    
    case 'TOGGLE_SIGN_OUT_MODAL':
      return { ...state, isSignOutModalOpen: action.payload };
    
    case 'SET_PAYMENT_METHOD':
      return { ...state, paymentMethod: action.payload };
    
    
    case 'RESET_ORDER':
      return {
        ...initialState,
        user: state.user
      };
    
    default:
      return state;
  }
};

export const AppProvider = ({ children }) => {
  const [state, dispatch] = useReducer(appReducer, initialState);

  // Load user from localStorage on mount
  useEffect(() => {
    const initializeApp = async () => {
      const storedUser = authService.getStoredUser();
      if (storedUser && authService.isAuthenticated()) {
        dispatch({ type: 'SET_USER', payload: storedUser });
        dispatch({ type: 'SET_SCREEN', payload: 'welcome' });
        // Add a small delay to ensure token is fully available
        setTimeout(async () => {
          await loadCart();
        }, 100);
      }
    };
    
    initializeApp();
  }, []);

  // Load cart from API
  const loadCart = async () => {
    // Only load cart if user is authenticated
    if (!authService.isAuthenticated()) {
      return;
    }

    try {
      const cartData = await cartService.getCart();
      dispatch({ type: 'SET_CART', payload: cartData });
      
      if (cartData.expires_at) {
        updateCartTimer(cartData.expires_at);
      }
    } catch (error) {
      console.error('Error loading cart:', error);
    }
  };

  // Update cart timer
  const updateCartTimer = (expiresAt) => {
    const timeRemaining = cartService.getTimeRemaining(expiresAt);
    const isExpired = cartService.isCartExpired(expiresAt);
    
    dispatch({ 
      type: 'UPDATE_CART_TIME', 
      payload: { timeRemaining, isExpired } 
    });

    if (timeRemaining > 0) {
      setTimeout(() => updateCartTimer(expiresAt), 1000);
    } else if (!isExpired) {
      // Cart just expired, clear it
      dispatch({ type: 'CLEAR_CART' });
    }
  };

  // Cart operations
  const addToCart = async (productId, quantity, variations = [], addons = [], addonQtys = []) => {
    try {
      const result = await cartService.addToCart(productId, quantity, variations, addons, addonQtys);
      if (result.success) {
        await loadCart(); // Reload cart from API
      }
      return result;
    } catch (error) {
      console.error('Error adding to cart:', error);
      return { success: false, message: 'Failed to add item to cart' };
    }
  };

  const updateCartItem = async (itemKey, quantity) => {
    try {
      const result = await cartService.updateCartItem(itemKey, quantity);
      if (result.success) {
        await loadCart(); // Reload cart from API
      }
      return result;
    } catch (error) {
      console.error('Error updating cart:', error);
      return { success: false, message: 'Failed to update cart' };
    }
  };

  const removeFromCart = async (itemKey) => {
    try {
      const result = await cartService.removeFromCart(itemKey);
      if (result.success) {
        await loadCart(); // Reload cart from API
      }
      return result;
    } catch (error) {
      console.error('Error removing from cart:', error);
      return { success: false, message: 'Failed to remove item from cart' };
    }
  };

  const clearCart = async () => {
    try {
      const result = await cartService.clearCart();
      if (result.success) {
        dispatch({ type: 'CLEAR_CART' });
      }
      return result;
    } catch (error) {
      console.error('Error clearing cart:', error);
      return { success: false, message: 'Failed to clear cart' };
    }
  };

  const startOver = async () => {
    try {
      const result = await cartService.startOver();
      if (result.success) {
        dispatch({ type: 'CLEAR_CART' });
      }
      return result;
    } catch (error) {
      console.error('Error starting over:', error);
      return { success: false, message: 'Failed to start over' };
    }
  };

  const value = {
    state,
    dispatch,
    // Cart operations
    addToCart,
    updateCartItem,
    removeFromCart,
    clearCart,
    startOver,
    loadCart
  };

  return (
    <AppContext.Provider value={value}>
      {children}
    </AppContext.Provider>
  );
};

export const useApp = () => {
  const context = useContext(AppContext);
  if (!context) {
    throw new Error('useApp must be used within an AppProvider');
  }
  return context;
};
