import { useApp } from '../context/AppContext';
import SignOutButton from './SignOutButton';

const Welcome = () => {
  const { state, dispatch } = useApp();

  const handleStartOrder = () => {
    dispatch({ type: 'SET_SCREEN', payload: 'menu' });
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-2xl p-8 md:p-12 w-full max-w-2xl text-center page-transition">
        <div className="text-8xl md:text-9xl mb-6">🍽️</div>
        
        <h1 className="text-3xl md:text-4xl font-bold text-secondary mb-4">
          Welcome, {state.user?.name || 'Guest'}!
        </h1>
        
        <p className="text-lg md:text-xl text-gray-600 mb-8">
          Ready to place your order? Browse our delicious menu and create your perfect meal.
        </p>
        
        <div className="space-y-4">
          <button
            onClick={handleStartOrder}
            className="btn-primary text-xl px-8 py-4 ripple w-full md:w-auto"
          >
            Start Your Order
          </button>
          
          <div className="flex justify-center">
            <SignOutButton />
          </div>
        </div>
        
        <div className="mt-8 text-sm text-gray-500">
          <p>Use the touch screen to navigate through our menu</p>
        </div>
      </div>
    </div>
  );
};

export default Welcome;
