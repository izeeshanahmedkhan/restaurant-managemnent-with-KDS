import { useState } from 'react';
import { useApp } from '../context/AppContext';
import { authService } from '../services/authService';

const SignOutButton = () => {
  const { dispatch } = useApp();
  const [isLoading, setIsLoading] = useState(false);

  const handleSignOut = async () => {
    setIsLoading(true);
    
    try {
      await authService.signOut();
      dispatch({ type: 'RESET_ORDER' });
      dispatch({ type: 'SET_SCREEN', payload: 'signin' });
    } catch (error) {
      console.error('Sign out error:', error);
      // Still reset the order even if API call fails
      dispatch({ type: 'RESET_ORDER' });
      dispatch({ type: 'SET_SCREEN', payload: 'signin' });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <button
      onClick={handleSignOut}
      disabled={isLoading}
      className="btn-secondary px-6 py-2 ripple disabled:opacity-50 disabled:cursor-not-allowed"
    >
      {isLoading ? (
        <div className="flex items-center">
          <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
          Signing Out...
        </div>
      ) : (
        'Sign Out'
      )}
    </button>
  );
};

export default SignOutButton;
