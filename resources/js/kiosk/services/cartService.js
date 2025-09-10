const API_BASE = '/api/v1/kiosk';

// Helper function to get auth headers
const getAuthHeaders = () => {
  const token = localStorage.getItem('kiosk_token');
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

export const cartService = {
  async getCart() {
    try {
      const response = await fetch(`${API_BASE}/cart`, {
        method: 'GET',
        headers: getAuthHeaders()
      });

      const data = await response.json();
      return data.success ? data.data : { items: [], total: 0, item_count: 0, expires_at: null };
    } catch (error) {
      console.error('Error fetching cart:', error);
      return { items: [], total: 0, item_count: 0, expires_at: null };
    }
  },

  async addToCart(productId, quantity, variations = [], addons = [], addonQtys = []) {
    try {
      console.log('CartService - addToCart called with:', {
        productId,
        quantity,
        variations,
        addons,
        addonQtys
      });
      
      const requestBody = {
        product_id: productId,
        quantity: quantity,
        variations: variations,
        add_ons: addons,
        add_on_qtys: addonQtys
      };
      
      console.log('CartService - request body:', requestBody);
      
      const response = await fetch(`${API_BASE}/cart/add`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify(requestBody)
      });

      console.log('CartService - response status:', response.status);
      
      const data = await response.json();
      console.log('CartService - response data:', data);
      
      return data;
    } catch (error) {
      console.error('Error adding to cart:', error);
      return { success: false, message: 'Network error' };
    }
  },

  async updateCartItem(itemKey, quantity) {
    try {
      const response = await fetch(`${API_BASE}/cart/update`, {
        method: 'PUT',
        headers: getAuthHeaders(),
        body: JSON.stringify({
          item_key: itemKey,
          quantity: quantity
        })
      });

      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Error updating cart:', error);
      return { success: false, message: 'Network error' };
    }
  },

  async removeFromCart(itemKey) {
    try {
      const response = await fetch(`${API_BASE}/cart/remove`, {
        method: 'DELETE',
        headers: getAuthHeaders(),
        body: JSON.stringify({
          item_key: itemKey
        })
      });

      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Error removing from cart:', error);
      return { success: false, message: 'Network error' };
    }
  },

  async clearCart() {
    try {
      const response = await fetch(`${API_BASE}/cart/clear`, {
        method: 'DELETE',
        headers: getAuthHeaders()
      });

      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Error clearing cart:', error);
      return { success: false, message: 'Network error' };
    }
  },

  async startOver() {
    try {
      const response = await fetch(`${API_BASE}/cart/start-over`, {
        method: 'POST',
        headers: getAuthHeaders()
      });

      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Error starting over:', error);
      return { success: false, message: 'Network error' };
    }
  },

  // Helper function to generate item key for cart
  generateItemKey(productId, variations, addons) {
    return `${productId}_${JSON.stringify(variations)}_${JSON.stringify(addons)}`;
  },

  // Helper function to check if cart is expired
  isCartExpired(expiresAt) {
    if (!expiresAt) return false;
    return new Date(expiresAt) <= new Date();
  },

  // Helper function to get time remaining until expiration
  getTimeRemaining(expiresAt) {
    if (!expiresAt) return 0;
    const now = new Date();
    const expiry = new Date(expiresAt);
    const diff = expiry - now;
    return Math.max(0, Math.floor(diff / 1000)); // Return seconds remaining
  }
};
