import { useApp } from '../context/AppContext';

const SignOutModal = ({ isOpen, onClose }) => {
  const { dispatch } = useApp();

  const handleSignOut = () => {
    dispatch({ type: 'SET_USER', payload: null });
    dispatch({ type: 'SET_SCREEN', payload: 'signin' });
    onClose();
  };

  if (!isOpen) return null;

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content" onClick={(e) => e.stopPropagation()}>
        <div className="p-6 text-center">
          <div className="text-6xl mb-4">🚪</div>
          <h2 className="text-2xl font-bold text-secondary mb-4">Sign Out</h2>
          <p className="text-gray-600 mb-6">
            Are you sure you want to sign out? You'll need to sign in again to place orders.
          </p>
          
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button
              onClick={onClose}
              className="btn-secondary px-6 py-3 ripple"
            >
              Cancel
            </button>
            <button
              onClick={handleSignOut}
              className="btn-accent px-6 py-3 ripple"
            >
              Sign Out
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SignOutModal;

