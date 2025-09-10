import { useApp } from './context/AppContext';
import SignIn from './components/SignIn';
import Welcome from './components/Welcome';
import Menu from './components/Menu';
import Checkout from './components/Checkout';
import OrderComplete from './components/OrderComplete';
import ProductModal from './components/ProductModal';
import './styles/index.css';

const App = () => {
  const { state, dispatch } = useApp();

  const renderScreen = () => {
    switch (state.currentScreen) {
      case 'signin':
        return <SignIn />;
      case 'welcome':
        return <Welcome />;
      case 'menu':
        return <Menu />;
      case 'checkout':
        return <Checkout />;
      case 'complete':
        return <OrderComplete />;
      default:
        return <SignIn />;
    }
  };

  return (
    <div className="App">
      {renderScreen()}
      
      {/* Product Modal - Available on all screens */}
      {state.isProductModalOpen && state.selectedProduct && (
        <ProductModal
          product={state.selectedProduct}
          onClose={() => {
            dispatch({ type: 'TOGGLE_PRODUCT_MODAL', payload: false });
            dispatch({ type: 'SET_SELECTED_PRODUCT', payload: null });
            dispatch({ type: 'SET_EDITING_CART_ITEM', payload: null });
          }}
        />
      )}
    </div>
  );
};

export default App;
