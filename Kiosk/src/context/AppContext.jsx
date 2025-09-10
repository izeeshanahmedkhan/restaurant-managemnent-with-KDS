import { createContext, useContext, useReducer } from 'react';

const AppContext = createContext();

const initialState = {
  currentScreen: 'signin', // signin, welcome, menu, checkout, complete
  user: null,
  cart: [],
  selectedProduct: null,
  orderNumber: null,
  searchQuery: '',
  selectedCategory: null,
  isProductModalOpen: false,
  isCartOpen: false,
  isSignOutModalOpen: false,
  paymentMethod: null
};

const appReducer = (state, action) => {
  switch (action.type) {
    case 'SET_SCREEN':
      return { ...state, currentScreen: action.payload };
    
    case 'SET_USER':
      return { ...state, user: action.payload };
    
    case 'ADD_TO_CART': {
      const existingItem = state.cart.find(
        item => item.id === action.payload.id && 
        JSON.stringify(item.modifiers) === JSON.stringify(action.payload.modifiers)
      );
      
      if (existingItem) {
        return {
          ...state,
          cart: state.cart.map(item =>
            item.id === action.payload.id && 
            JSON.stringify(item.modifiers) === JSON.stringify(action.payload.modifiers)
              ? { ...item, quantity: item.quantity + 1 }
              : item
          )
        };
      }
      
      return {
        ...state,
        cart: [...state.cart, { ...action.payload, quantity: 1 }]
      };
    }
    
    case 'UPDATE_CART_ITEM_QUANTITY':
      return {
        ...state,
        cart: state.cart.map(item =>
          item.id === action.payload.id && 
          JSON.stringify(item.modifiers) === JSON.stringify(action.payload.modifiers)
            ? { ...item, quantity: action.payload.quantity }
            : item
        ).filter(item => item.quantity > 0)
      };
    
    case 'REMOVE_FROM_CART':
      return {
        ...state,
        cart: state.cart.filter(item =>
          !(item.id === action.payload.id && 
            JSON.stringify(item.modifiers) === JSON.stringify(action.payload.modifiers))
        )
      };
    
    case 'CLEAR_CART':
      return { ...state, cart: [] };
    
    case 'SET_SELECTED_PRODUCT':
      return { ...state, selectedProduct: action.payload };
    
    case 'SET_ORDER_NUMBER':
      return { ...state, orderNumber: action.payload };
    
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

  const value = {
    state,
    dispatch
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
