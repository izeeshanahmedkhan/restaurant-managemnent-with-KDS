import { useState, useMemo, useEffect } from 'react';
import { useApp } from '../context/AppContext';
import { menuService } from '../services/menuService';
import ProductCard from './ProductCard';
import CartDrawer from './CartDrawer';

const Menu = () => {
  const { state, dispatch } = useApp();
  const [searchQuery, setSearchQuery] = useState('');
  const [menuData, setMenuData] = useState({ categories: [], products: [] });
  const [isLoading, setIsLoading] = useState(true);

  // Don't render if user is not authenticated
  if (!state.user) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center p-4">
        <div className="bg-white rounded-2xl shadow-2xl p-8 text-center">
          <div className="text-6xl mb-4">🔒</div>
          <h2 className="text-2xl font-bold text-gray-800 mb-2">Authentication Required</h2>
          <p className="text-gray-600">Please sign in to access the menu.</p>
        </div>
      </div>
    );
  }

  useEffect(() => {
    const loadMenu = async () => {
      console.log('Menu useEffect - User:', state.user, 'Authenticated:', !!state.user);
      
      // Only load menu if user is authenticated
      if (!state.user) {
        console.log('No user found, skipping menu load');
        setIsLoading(false);
        return;
      }

      try {
        console.log('Loading menu data...');
        setIsLoading(true);
        const [categories, products] = await Promise.all([
          menuService.getCategories(),
          menuService.getProducts()
        ]);
        console.log('Menu data loaded:', { categories, products });
        setMenuData({ categories, products: products.data || [] });
      } catch (error) {
        console.error('Error loading menu:', error);
      } finally {
        setIsLoading(false);
      }
    };

    loadMenu();
  }, [state.user]);

  const filteredProducts = useMemo(() => {
    let products = menuData.products || [];

    // Filter by category
    if (state.selectedCategory) {
      products = products.filter(product => {
        // Check if product has category_ids and if it contains the selected category
        if (product.category_ids && Array.isArray(product.category_ids)) {
          return product.category_ids.some(cat => cat.id === state.selectedCategory.toString());
        }
        return false;
      });
    }

    // Filter by search query
    if (searchQuery) {
      products = products.filter(product =>
        product.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        product.description.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }

    return products;
  }, [state.selectedCategory, searchQuery, menuData.products]);

  const handleCategorySelect = (categoryId) => {
    dispatch({ type: 'SET_SELECTED_CATEGORY', payload: categoryId });
  };

  const handleProductClick = async (product) => {
    try {
      // Fetch detailed product data with variations
      const detailedProduct = await menuService.getProduct(product.id);
      if (detailedProduct) {
        dispatch({ type: 'SET_SELECTED_PRODUCT', payload: detailedProduct });
        dispatch({ type: 'TOGGLE_PRODUCT_MODAL', payload: true });
      } else {
        console.error('Failed to fetch detailed product data');
        // Fallback to basic product data
        dispatch({ type: 'SET_SELECTED_PRODUCT', payload: product });
        dispatch({ type: 'TOGGLE_PRODUCT_MODAL', payload: true });
      }
    } catch (error) {
      console.error('Error fetching product details:', error);
      // Fallback to basic product data
      dispatch({ type: 'SET_SELECTED_PRODUCT', payload: product });
      dispatch({ type: 'TOGGLE_PRODUCT_MODAL', payload: true });
    }
  };

  const handleSearchChange = async (e) => {
    const query = e.target.value;
    setSearchQuery(query);
    
    if (query.trim()) {
      try {
        const searchResults = await menuService.searchProducts(query);
        setMenuData(prev => ({ ...prev, products: searchResults }));
      } catch (error) {
        console.error('Search error:', error);
      }
    } else {
      // Reload all products when search is cleared
      try {
        const products = await menuService.getProducts();
        setMenuData(prev => ({ ...prev, products: products.data || [] }));
      } catch (error) {
        console.error('Error reloading products:', error);
      }
    }
  };

  const handleCartToggle = () => {
    dispatch({ type: 'TOGGLE_CART', payload: !state.isCartOpen });
  };

  const handleCheckout = () => {
    dispatch({ type: 'SET_SCREEN', payload: 'checkout' });
    dispatch({ type: 'TOGGLE_CART', payload: false });
  };

  if (isLoading) {
    return (
      <div className="min-h-screen bg-background flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
          <p className="text-lg text-gray-600">Loading menu...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <div className="bg-white shadow-lg p-4">
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-col lg:flex-row items-center justify-between gap-4">
            <div className="flex items-center space-x-4">
              <button
                onClick={() => dispatch({ type: 'SET_SCREEN', payload: 'welcome' })}
                className="btn-secondary px-4 py-2 ripple"
              >
                ← Back
              </button>
              <h1 className="text-xl lg:text-2xl font-bold text-secondary">Menu</h1>
            </div>
            
            <div className="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full lg:w-auto">
              <div className="relative w-full sm:w-auto">
                <input
                  type="text"
                  placeholder="Search menu items..."
                  value={searchQuery}
                  onChange={handleSearchChange}
                  className="input-field pr-10 w-full sm:w-64"
                />
                <div className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                  🔍
                </div>
              </div>
              
              <button
                onClick={handleCartToggle}
                className="btn-primary px-6 py-2 ripple relative w-full sm:w-auto"
              >
                <span className="flex items-center justify-center">
                  Cart
                  {state.cart.item_count > 0 && (
                    <span className="ml-2 bg-white text-red-500 text-xs rounded-full h-5 w-5 flex items-center justify-center min-w-[20px] font-bold">
                      {state.cart.item_count}
                    </span>
                  )}
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto flex flex-col lg:flex-row min-h-screen">
        {/* Sidebar */}
        <div className="w-full lg:w-64 bg-white shadow-lg p-4 lg:min-h-screen">
          <h2 className="text-lg font-semibold text-secondary mb-4">Categories</h2>
          <div className="grid grid-cols-2 lg:grid-cols-1 gap-2 lg:space-y-2 lg:space-y-0">
            <button
              onClick={() => handleCategorySelect(null)}
              className={`text-left p-3 rounded-lg transition-all duration-200 ${
                state.selectedCategory === null
                  ? 'bg-primary text-white'
                  : 'hover:bg-gray-100 text-gray-700'
              }`}
            >
              All Items
            </button>
            {menuData.categories.map(category => (
              <button
                key={category.id}
                onClick={() => handleCategorySelect(category.id)}
                className={`text-left p-3 rounded-lg transition-all duration-200 flex items-center space-x-2 ${
                  state.selectedCategory === category.id
                    ? 'bg-primary text-white'
                    : 'hover:bg-gray-100 text-gray-700'
                }`}
              >
                <span className="text-lg">{category.icon || '🍽️'}</span>
                <span className="text-sm lg:text-base">{category.name}</span>
              </button>
            ))}
          </div>
        </div>

        {/* Product Grid */}
        <div className="flex-1 p-4 lg:p-6 overflow-x-hidden">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
            {filteredProducts.map(product => (
              <ProductCard
                key={product.id}
                product={product}
                onClick={() => handleProductClick(product)}
              />
            ))}
          </div>
          
          {filteredProducts.length === 0 && (
            <div className="text-center py-12">
              <div className="text-6xl mb-4">🔍</div>
              <h3 className="text-xl font-semibold text-gray-600 mb-2">No items found</h3>
              <p className="text-gray-500">Try adjusting your search or category filter</p>
            </div>
          )}
        </div>
      </div>


      {/* Cart Drawer */}
      {state.isCartOpen && (
        <CartDrawer
          onClose={() => dispatch({ type: 'TOGGLE_CART', payload: false })}
          onCheckout={handleCheckout}
        />
      )}
    </div>
  );
};

export default Menu;
