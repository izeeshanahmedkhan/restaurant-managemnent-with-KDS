const API_BASE = '/api/v1/kiosk';

// Helper function to get auth headers
const getAuthHeaders = () => {
  const token = localStorage.getItem('kiosk_token');
  console.log('Auth headers - Token:', token ? 'Present' : 'Missing');
  return {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    'Authorization': token ? `Bearer ${token}` : '',
    'X-Session-ID': getSessionId()
  };
};

// Generate or get session ID
const getSessionId = () => {
  let sessionId = localStorage.getItem('kiosk_session_id');
  if (!sessionId) {
    sessionId = 'kiosk_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    localStorage.setItem('kiosk_session_id', sessionId);
  }
  return sessionId;
};

export const authService = {
  async signIn(email, password) {
    try {
      const response = await fetch(`${API_BASE}/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ email, password })
      });

      const data = await response.json();
      
      if (data.success && data.data.token) {
        // Store token and user data
        console.log('Storing token:', data.data.token);
        localStorage.setItem('kiosk_token', data.data.token);
        localStorage.setItem('kiosk_user', JSON.stringify(data.data.user));
        console.log('Token stored successfully');
      }
      
      return data;
    } catch (error) {
      console.error('Auth error:', error);
      return {
        success: false,
        message: 'Network error'
      };
    }
  },

  async signOut() {
    try {
      const response = await fetch(`${API_BASE}/auth/logout`, {
        method: 'POST',
        headers: getAuthHeaders()
      });

      // Clear stored data regardless of response
      localStorage.removeItem('kiosk_token');
      localStorage.removeItem('kiosk_user');
      localStorage.removeItem('kiosk_session_id');

      return await response.json();
    } catch (error) {
      // Clear stored data even on error
      localStorage.removeItem('kiosk_token');
      localStorage.removeItem('kiosk_user');
      localStorage.removeItem('kiosk_session_id');
      return { success: false };
    }
  },

  async getCurrentUser() {
    try {
      const response = await fetch(`${API_BASE}/auth/me`, {
        method: 'GET',
        headers: getAuthHeaders()
      });

      const data = await response.json();
      
      if (data.success) {
        localStorage.setItem('kiosk_user', JSON.stringify(data.data.user));
      }
      
      return data;
    } catch (error) {
      return { success: false };
    }
  },

  isAuthenticated() {
    return !!localStorage.getItem('kiosk_token');
  },

  getStoredUser() {
    const user = localStorage.getItem('kiosk_user');
    return user ? JSON.parse(user) : null;
  }
};
