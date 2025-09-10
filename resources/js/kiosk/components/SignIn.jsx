import { useState } from 'react';
import { useApp } from '../context/AppContext';
import { authService } from '../services/authService';

const SignIn = () => {
  const { dispatch } = useApp();
  const [formData, setFormData] = useState({
    email: '',
    password: ''
  });
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');

  const handleInputChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
    setError('');
  };

  const handleDemoFill = () => {
    setFormData({
      email: 'kiosk@restaurant.com',
      password: 'password123'
    });
    setError('');
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    setError('');

    try {
      const result = await authService.signIn(formData.email, formData.password);
      
      if (result.success) {
        dispatch({ type: 'SET_USER', payload: result.data.user });
        dispatch({ type: 'SET_SCREEN', payload: 'welcome' });
      } else {
        setError(result.message || 'Login failed');
      }
    } catch (err) {
      setError('An error occurred. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-md page-transition">
        <div className="text-center mb-6 md:mb-8">
          <div className="text-5xl md:text-6xl mb-4">🍽️</div>
          <h1 className="text-2xl md:text-3xl font-bold text-secondary mb-2">Restaurant Kiosk</h1>
          <p className="text-sm md:text-base text-gray-600">Please sign in to start your order</p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4 md:space-y-6">
          <div>
            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
              Email Address
            </label>
            <input
              type="email"
              id="email"
              name="email"
              value={formData.email}
              onChange={handleInputChange}
              className="input-field text-base"
              placeholder="Enter your email"
              required
            />
          </div>

          <div>
            <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
              Password
            </label>
            <input
              type="password"
              id="password"
              name="password"
              value={formData.password}
              onChange={handleInputChange}
              className="input-field text-base"
              placeholder="Enter your password"
              autoComplete="current-password"
              required
            />
          </div>

          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
              {error}
            </div>
          )}

          <button
            type="submit"
            disabled={isLoading}
            className="btn-primary w-full ripple disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isLoading ? (
              <div className="flex items-center justify-center">
                <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                Signing In...
              </div>
            ) : (
              'Sign In'
            )}
          </button>
        </form>

        <div className="mt-4 md:mt-6">
          <button
            type="button"
            onClick={handleDemoFill}
            className="btn-secondary w-full py-2 text-sm ripple mb-3"
          >
            🔧 Fill Demo Credentials
          </button>
          
          <div className="text-center text-xs md:text-sm text-gray-600">
            <p className="mb-1">Demo Credentials:</p>
            <p className="break-all">Email: kiosk@restaurant.com</p>
            <p>Password: password123</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SignIn;
