import { useApp } from '../context/AppContext';
import SignOutButton from './SignOutButton';
import SignOutModal from './SignOutModal';

const Welcome = () => {
  const { state, dispatch } = useApp();

  const handleStartOrder = () => {
    dispatch({ type: 'SET_SCREEN', payload: 'menu' });
  };



  return (
    <div className="min-h-screen bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-2xl text-center page-transition">
        <div className="text-8xl mb-6">🍽️</div>
        
        <h1 className="text-3xl md:text-4xl font-bold text-secondary mb-4">
          Welcome to Our Restaurant!
        </h1>
        
        <p className="text-lg md:text-xl text-gray-600 mb-8">
          Ready to place your order? Use our self-service kiosk to browse our menu and customize your meal.
        </p>

        <div className="space-y-4">
          <button
            onClick={handleStartOrder}
            className="btn-primary text-xl md:text-2xl px-8 md:px-12 py-4 ripple w-full md:w-auto"
          >
            Start Order
          </button>
        </div>

        <div className="mt-8 text-sm text-gray-500">
          <p>{state.user?.name && `Signed in as: ${state.user.name}`}</p>
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

export default Welcome;
