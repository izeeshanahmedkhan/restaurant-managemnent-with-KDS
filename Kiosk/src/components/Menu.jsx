import { useState, useMemo } from 'react';
import { useApp } from '../context/AppContext';
import { menuData } from '../data/menuData';
import ProductCard from './ProductCard';
import ProductModal from './ProductModal';
import CartDrawer from './CartDrawer';

const Menu = () => {
  const { state, dispatch } = useApp();
  const [searchQuery, setSearchQuery] = useState('');

  const filteredProducts = useMemo(() => {
    let products = menuData.products;

    // Filter by category
    if (state.selectedCategory) {
      products = products.filter(product => product.categoryId === state.selectedCategory);
    }

    // Filter by search query
    if (searchQuery) {
      products = products.filter(product =>
        product.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        product.description.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }

    return products;
  }, [state.selectedCategory, searchQuery]);

  const handleCategorySelect = (categoryId) => {
    dispatch({ type: 'SET_SELECTED_CATEGORY', payload: categoryId });
  };

  const handleProductClick = (product) => {
    dispatch({ type: 'SET_SELECTED_PRODUCT', payload: product });
    dispatch({ type: 'TOGGLE_PRODUCT_MODAL', payload: true });
  };

  const handleSearchChange = (e) => {
    setSearchQuery(e.target.value);
  };

  const handleCartToggle = () => {
    dispatch({ type: 'TOGGLE_CART', payload: !state.isCartOpen });
  };

  const handleCheckout = () => {
    dispatch({ type: 'SET_SCREEN', payload: 'checkout' });
    dispatch({ type: 'TOGGLE_CART', payload: false });
  };


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
                  Cart ({state.cart.length})
                  {state.cart.length > 0 && (
                    <span className="ml-2 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center min-w-[20px]">
                      {state.cart.reduce((sum, item) => sum + item.quantity, 0)}
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
                <span className="text-lg">{category.icon}</span>
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

      {/* Product Modal */}
      {state.isProductModalOpen && state.selectedProduct && (
        <ProductModal
          product={state.selectedProduct}
          onClose={() => {
            dispatch({ type: 'TOGGLE_PRODUCT_MODAL', payload: false });
            dispatch({ type: 'SET_SELECTED_PRODUCT', payload: null });
          }}
        />
      )}

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
