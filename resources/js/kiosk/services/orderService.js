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

export const orderService = {
  async createOrder(orderData) {
    try {
      const response = await fetch(`${API_BASE}/orders`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify(orderData)
      });

      const data = await response.json();
      return data;
    } catch (error) {
      console.error('Order creation error:', error);
      return {
        success: false,
        message: 'Network error'
      };
    }
  },

  async getOrders(page = 1, status = null) {
    try {
      let url = `${API_BASE}/orders?page=${page}`;
      if (status) url += `&status=${status}`;

      const response = await fetch(url, {
        method: 'GET',
        headers: getAuthHeaders()
      });

      const data = await response.json();
      return data.success ? data.data : { data: [], current_page: 1, last_page: 1 };
    } catch (error) {
      console.error('Error fetching orders:', error);
      return { data: [], current_page: 1, last_page: 1 };
    }
  },

  async getOrder(id) {
    try {
      const response = await fetch(`${API_BASE}/orders/${id}`, {
        method: 'GET',
        headers: getAuthHeaders()
      });

      const data = await response.json();
      return data.success ? data.data : null;
    } catch (error) {
      console.error('Error fetching order:', error);
      return null;
    }
  },

  async getReceipt(id) {
    try {
      const response = await fetch(`${API_BASE}/orders/${id}/receipt`, {
        method: 'GET',
        headers: getAuthHeaders()
      });

      const data = await response.json();
      return data.success ? data.data : null;
    } catch (error) {
      console.error('Error fetching receipt:', error);
      return null;
    }
  },

  async printReceipt(orderId) {
    try {
      const receiptData = await this.getReceipt(orderId);
      if (!receiptData) {
        throw new Error('Receipt not found');
      }

      // Create a new window for printing
      const printWindow = window.open('', '_blank');
      printWindow.document.write(receiptData.receipt_html);
      printWindow.document.close();
      
      // Wait for content to load then print
      printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
      };

      return { success: true };
    } catch (error) {
      console.error('Print error:', error);
      return { success: false, message: 'Print failed' };
    }
  }
};
