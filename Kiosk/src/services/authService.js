import { users } from '../data/menuData.js';

export const authService = {
  // Simulate API call delay
  delay: (ms) => new Promise(resolve => setTimeout(resolve, ms)),

  async signIn(email, password) {
    await this.delay(1000); // Simulate network delay
    
    const user = users.find(u => u.email === email && u.password === password);
    
    if (user) {
      return {
        success: true,
        user: {
          id: user.id,
          email: user.email,
          name: user.name
        }
      };
    }
    
    return {
      success: false,
      error: 'Invalid email or password'
    };
  },

  async signOut() {
    await this.delay(500);
    return { success: true };
  }
};
