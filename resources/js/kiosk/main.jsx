import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'
import { AppProvider } from './context/AppContext.jsx'

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
  const rootElement = document.getElementById('kiosk-app');
  if (rootElement) {
    const root = createRoot(rootElement);
    root.render(
      <StrictMode>
        <AppProvider>
          <App />
        </AppProvider>
      </StrictMode>
    );
  }
});
