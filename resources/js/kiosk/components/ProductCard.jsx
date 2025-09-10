const ProductCard = ({ product, onClick }) => {
  return (
    <div
      onClick={onClick}
      className="card cursor-pointer group hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full"
    >
      <div className="aspect-[4/3] bg-gray-200 rounded-t-xl overflow-hidden">
        <img
          src={product.image || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400&h=300&fit=crop'}
          alt={product.name}
          className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
          onError={(e) => {
            e.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzY2NjY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
          }}
        />
      </div>
      
      <div className="p-3 lg:p-4">
        <h3 className="text-base lg:text-lg font-semibold text-secondary mb-2 line-clamp-2">
          {product.name}
        </h3>
        
        <p className="text-xs lg:text-sm text-gray-600 mb-3 line-clamp-2">
          {product.description}
        </p>
        
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
          <span className="text-xl lg:text-2xl font-bold text-primary">
            ${(product.price || 0).toFixed(2)}
          </span>
          
          <button className="btn-primary px-3 py-2 text-xs lg:text-sm ripple w-full sm:w-auto">
            Customize
          </button>
        </div>
      </div>
    </div>
  );
};

export default ProductCard;
