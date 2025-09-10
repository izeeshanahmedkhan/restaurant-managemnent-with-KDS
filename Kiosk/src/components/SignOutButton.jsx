import { useApp } from '../context/AppContext';

const SignOutButton = () => {
  const { dispatch } = useApp();

  const handleSignOutClick = () => {
    dispatch({ type: 'TOGGLE_SIGN_OUT_MODAL', payload: true });
  };

  return (
    <button
      onClick={handleSignOutClick}
      className="fixed top-4 right-4 btn-secondary px-4 py-2 text-sm ripple z-40"
    >
      Sign Out
    </button>
  );
};

export default SignOutButton;
