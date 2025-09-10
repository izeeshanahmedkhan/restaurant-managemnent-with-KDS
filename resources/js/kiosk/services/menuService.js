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

export const menuService = {
  async getCategories() {
    try {
      const response = await fetch(`${API_BASE}/categories`, {
        method: 'GET',
        headers: getAuthHeaders()
      });
      const data = await response.json();
      return data.success ? data.data : [];
    } catch (error) {
      console.error('Error fetching categories:', error);
      return [];
    }
  },

  async getProducts(categoryId = null, search = null, page = 1) {
    try {
      let url = `${API_BASE}/products?page=${page}`;
      if (categoryId) url += `&category_id=${categoryId}`;
      if (search) url += `&search=${encodeURIComponent(search)}`;

      const response = await fetch(url, {
        method: 'GET',
        headers: getAuthHeaders()
      });
      const data = await response.json();
      return data.success ? data.data : { data: [], current_page: 1, last_page: 1 };
    } catch (error) {
      console.error('Error fetching products:', error);
      return { data: [], current_page: 1, last_page: 1 };
    }
  },

  async getProduct(id) {
    try {
      const response = await fetch(`${API_BASE}/products/${id}`, {
        method: 'GET',
        headers: getAuthHeaders()
      });
      const data = await response.json();
      return data.success ? data.data : null;
    } catch (error) {
      console.error('Error fetching product:', error);
      return null;
    }
  },

  async searchProducts(query) {
    try {
      const response = await fetch(`${API_BASE}/products/search?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: getAuthHeaders()
      });
      const data = await response.json();
      return data.success ? data.data : [];
    } catch (error) {
      console.error('Error searching products:', error);
      return [];
    }
  },

  async getAddons() {
    try {
      const response = await fetch(`${API_BASE}/addons`, {
        method: 'GET',
        headers: getAuthHeaders()
      });
      const data = await response.json();
      return data.success ? data.data : [];
    } catch (error) {
      console.error('Error fetching addons:', error);
      return [];
    }
  },

  async getAttributes() {
    try {
      const response = await fetch(`${API_BASE}/attributes`, {
        method: 'GET',
        headers: getAuthHeaders()
      });
      const data = await response.json();
      return data.success ? data.data : [];
    } catch (error) {
      console.error('Error fetching attributes:', error);
      return [];
    }
  },

  async getBranch() {
    try {
      const response = await fetch(`${API_BASE}/branch`, {
        method: 'GET',
        headers: getAuthHeaders()
      });
      const data = await response.json();
      return data.success ? data.data : null;
    } catch (error) {
      console.error('Error fetching branch:', error);
      return null;
    }
  },

  async getSettings() {
    try {
      const response = await fetch(`${API_BASE}/settings`, {
        method: 'GET',
        headers: getAuthHeaders()
      });
      const data = await response.json();
      return data.success ? data.data : {};
    } catch (error) {
      console.error('Error fetching settings:', error);
      return {};
    }
  }
};
