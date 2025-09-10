import { useApp } from './context/AppContext';
import SignIn from './components/SignIn';
import Welcome from './components/Welcome';
import Menu from './components/Menu';
import Checkout from './components/Checkout';
import OrderComplete from './components/OrderComplete';
import './styles/index.css';

const App = () => {
  const { state } = useApp();

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
    </div>
  );
};

export default App;
