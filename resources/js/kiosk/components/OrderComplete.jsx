import { useApp } from '../context/AppContext';
import { useState, useEffect, useRef } from 'react';
import { orderService } from '../services/orderService';

const OrderComplete = () => {
  const { state, dispatch } = useApp();
  const [receiptData, setReceiptData] = useState(null);
  const [isLoadingReceipt, setIsLoadingReceipt] = useState(false);
  const [hasAutoPrinted, setHasAutoPrinted] = useState(false);
  const printRef = useRef(null);

  const handleNewOrder = () => {
    dispatch({ type: 'SET_SCREEN', payload: 'menu' });
  };

  const handlePrintReceipt = () => {
    if (printRef.current) {
      const printWindow = window.open('', '_blank');
      printWindow.document.write(`
        <html>
          <head>
            <title>Receipt - ${state.orderNumber}</title>
            <style>
              body { 
                font-family: Arial, sans-serif; 
                margin: 20px; 
                line-height: 1.4;
              }
              .header { 
                text-align: center; 
                border-bottom: 2px solid #333; 
                padding-bottom: 10px; 
                margin-bottom: 20px;
              }
              .item { 
                margin-bottom: 10px; 
                padding-bottom: 5px; 
                border-bottom: 1px solid #eee;
              }
              .item-name { 
                font-weight: bold; 
                font-size: 14px;
              }
              .item-details { 
                font-size: 12px; 
                color: #666; 
                margin-left: 10px;
              }
              .variation, .addon { 
                margin-left: 15px; 
                font-size: 11px; 
                color: #888;
              }
              .total { 
                font-weight: bold; 
                font-size: 16px; 
                border-top: 2px solid #333; 
                padding-top: 10px; 
                margin-top: 15px;
              }
              .order-info {
                margin-top: 20px;
                font-size: 12px;
                color: #666;
              }
            </style>
          </head>
          <body>
            <div class="header">
              <h2>Restaurant Receipt</h2>
              <p>Order #${state.orderNumber}</p>
            </div>
            
            ${receiptData.items.map(item => `
              <div class="item">
                <div class="item-name">${item.product_name}</div>
                <div class="item-details">
                  Quantity: ${item.quantity} × $${parseFloat(item.base_price || 0).toFixed(2)}
                  ${item.variation_price > 0 ? ` + $${parseFloat(item.variation_price || 0).toFixed(2)} variations` : ''}
                  ${item.addon_price > 0 ? ` + $${parseFloat(item.addon_price || 0).toFixed(2)} add-ons` : ''}
                </div>
                ${item.variations && item.variations.length > 0 ? `
                  <div class="variation">
                    <strong>Variations:</strong><br>
                    ${item.variations.map(v => `• ${v.name}: ${v.label} (+$${parseFloat(v.price || 0).toFixed(2)})`).join('<br>')}
                  </div>
                ` : ''}
                ${item.addons && item.addons.length > 0 ? `
                  <div class="addon">
                    <strong>Add-ons:</strong><br>
                    ${item.addons.map(a => `• ${a.name} (x${a.quantity}) (+$${parseFloat(a.price || 0).toFixed(2)})`).join('<br>')}
                  </div>
                ` : ''}
                                        <div style="text-align: right; font-weight: bold;">
                                            Total: $${parseFloat(item.total_price || 0).toFixed(2)}
                                        </div>
              </div>
            `).join('')}
            
            <div class="total">
              <div style="text-align: right;">
                                        Grand Total: $${receiptData.items ?
                                            receiptData.items.reduce((sum, item) =>
                                                sum + parseFloat(item.total_price || 0), 0
                                            ).toFixed(2) :
                                            parseFloat(receiptData.total || 0).toFixed(2)
                                        }
              </div>
            </div>
            
            <div class="order-info">
              <p><strong>Payment Method:</strong> ${receiptData.payment_method || 'Cash'}</p>
              <p><strong>Order Time:</strong> ${receiptData.created_at || new Date().toLocaleString()}</p>
              <p><strong>Thank you for your order!</strong></p>
            </div>
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.focus();
      printWindow.print();
      printWindow.close();
    }
  };


  // Load receipt data when component mounts
  useEffect(() => {
    const loadReceipt = async () => {
      if (state.orderNumber && !receiptData) {
        setIsLoadingReceipt(true);
        try {
          // Extract numeric ID from order number (e.g., "K-100018" -> "100018")
          const orderId = state.orderNumber ? state.orderNumber.replace('K-', '') : null;
          if (orderId) {
            const receipt = await orderService.getReceipt(orderId);
            if (receipt) {
              setReceiptData(receipt);
            }
          }
        } catch (error) {
          console.error('Error loading receipt:', error);
        } finally {
          setIsLoadingReceipt(false);
        }
      }
    };

    loadReceipt();
  }, [state.orderNumber, receiptData]);

  // Auto-print receipt when data is loaded
  useEffect(() => {
    if (receiptData && !hasAutoPrinted) {
      // Small delay to ensure the component is fully rendered
      const timer = setTimeout(() => {
        handlePrintReceipt();
        setHasAutoPrinted(true);
      }, 1000);
      
      return () => clearTimeout(timer);
    }
  }, [receiptData, hasAutoPrinted]);

  return (
    <div className="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-2xl p-6 md:p-8 w-full max-w-4xl text-center page-transition">
        <div className="text-6xl md:text-7xl mb-4">✅</div>
        
        <h1 className="text-2xl md:text-3xl font-bold text-secondary mb-4">
          Order Placed Successfully!
        </h1>
        
        <div className="bg-gray-50 rounded-lg p-4 mb-6">
          <h2 className="text-lg font-semibold text-secondary mb-2">Order Details</h2>
          <p className="text-base text-gray-600 mb-2">
            Order Number: <span className="font-bold text-primary">{state.orderNumber}</span>
          </p>
          <p className="text-sm text-gray-500">
            Please keep this number for reference
          </p>
        </div>
        
        {/* Receipt Preview */}
        {receiptData && (
          <div className="bg-white border border-gray-200 rounded-lg p-4 mb-6 text-left">
            <h3 className="text-lg font-semibold text-gray-800 mb-3 text-center">Receipt Preview</h3>
            
            {/* DEBUG SECTION */}
            <div className="bg-red-100 border border-red-300 rounded p-3 mb-4 text-xs">
              <h4 className="font-bold text-red-800 mb-2">DEBUG INFO:</h4>
              <div className="space-y-1">
                <div><strong>Raw Receipt Data:</strong> {JSON.stringify(receiptData, null, 2)}</div>
                {receiptData.items && receiptData.items.map((item, index) => (
                  <div key={index} className="mt-2 p-2 bg-red-50 rounded">
                    <div><strong>Item {index + 1}:</strong></div>
                    <div>• Base Price: {item.base_price}</div>
                    <div>• Variation Price: {item.variation_price}</div>
                    <div>• Addon Price: {item.addon_price}</div>
                    <div>• Discount Amount: {item.discount_amount}</div>
                    <div>• Total Price: {item.total_price}</div>
                    <div>• Variations: {JSON.stringify(item.variations)}</div>
                    <div>• Addons: {JSON.stringify(item.addons)}</div>
                    <div><strong>Calculated Single:</strong> {((item.base_price || 0) + (item.variation_price || 0) + (item.addon_price || 0) - (item.discount_amount || 0)).toFixed(2)}</div>
                    <div><strong>Expected Single:</strong> 180.00</div>
                  </div>
                ))}
              </div>
            </div>
            
            <div className="space-y-2 text-sm">
              {receiptData.items && receiptData.items.map((item, index) => (
                <div key={index} className="border-b border-gray-100 pb-2 last:border-b-0">
                  {/* Main Item */}
                  <div className="flex justify-between items-start mb-1">
                    <div className="flex-1">
                      <div className="font-medium text-gray-800">{item.product_name}</div>
                      <div className="text-xs text-gray-500 mt-1">
                        <div className="flex items-center gap-2 mb-1">
                          <span>Qty: {item.quantity}</span>
                          <span>•</span>
                          <span>Base: ${parseFloat(item.base_price || 0).toFixed(2)}</span>
                          {item.variation_price > 0 && (
                            <>
                              <span>•</span>
                              <span className="text-blue-600">Var: +${parseFloat(item.variation_price || 0).toFixed(2)}</span>
                            </>
                          )}
                          {item.addon_price > 0 && (
                            <>
                              <span>•</span>
                              <span className="text-orange-600">Add: +${parseFloat(item.addon_price || 0).toFixed(2)}</span>
                            </>
                          )}
                          {item.discount_amount > 0 && (
                            <>
                              <span>•</span>
                              <span className="text-red-600">Disc: -${parseFloat(item.discount_amount || 0).toFixed(2)}</span>
                            </>
                          )}
                        </div>
                        <div className="flex justify-between text-xs">
                          <span>Single: ${parseFloat((item.base_price || 0) + (item.variation_price || 0) + (item.addon_price || 0) - (item.discount_amount || 0)).toFixed(2)}</span>
                          <span className="font-bold text-green-600">Total: ${parseFloat(item.total_price || 0).toFixed(2)}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  {/* Variations & Add-ons */}
                  {(item.variations && item.variations.length > 0) || (item.addons && item.addons.length > 0) ? (
                    <div className="ml-2 mt-1">
                      {item.variations && item.variations.length > 0 && (
                        <div className="text-xs text-gray-500 mb-1">
                          <span className="text-gray-600 font-medium">Variations:</span> 
                          {item.variations.map((variation, vIndex) => (
                            <span key={vIndex}>
                              <span className="font-medium">{variation.name || 'Variation'}:</span> {variation.label || 'Option'}
                              {variation.price > 0 && (
                                <span className="text-blue-600 font-medium"> (+${parseFloat(variation.price).toFixed(2)})</span>
                              )}
                              {vIndex < item.variations.length - 1 && <span className="text-gray-400"> • </span>}
                            </span>
                          ))}
                        </div>
                      )}
                      
                      {item.addons && item.addons.length > 0 && (
                        <div className="text-xs text-gray-500">
                          <span className="text-gray-600 font-medium">Add-ons:</span> 
                          {item.addons.map((addon, aIndex) => (
                            <span key={aIndex}>
                              <span className="font-medium">{addon.name}</span>
                              {addon.quantity > 1 && (
                                <span className="text-orange-600"> (x{addon.quantity})</span>
                              )}
                              {addon.price > 0 && (
                                <span className="text-orange-600 font-medium"> (+${parseFloat(addon.price).toFixed(2)})</span>
                              )}
                              {aIndex < item.addons.length - 1 && <span className="text-gray-400"> • </span>}
                            </span>
                          ))}
                        </div>
                      )}
                    </div>
                  ) : null}
                </div>
              ))}
              
              {/* Total */}
              <div className="flex justify-between items-center pt-3 border-t border-gray-200 font-bold">
                <span className="text-base">Total:</span>
                <span className="text-lg text-green-600">
                  ${receiptData.items ? 
                    receiptData.items.reduce((sum, item) => 
                      sum + ((item.base_price || 0) + (item.variation_price || 0) + (item.addon_price || 0) - (item.discount_amount || 0)) * item.quantity, 0
                    ).toFixed(2) : 
                    parseFloat(receiptData.total || 0).toFixed(2)
                  }
                </span>
              </div>
            </div>
          </div>
        )}

        {isLoadingReceipt && (
          <div className="bg-gray-50 rounded-lg p-4 mb-6">
            <p className="text-sm text-gray-500">Loading receipt...</p>
          </div>
        )}
        
        <p className="text-base text-gray-600 mb-6">
          Thank you for your order! Please proceed to the counter to make payment and collect your food.
        </p>
        
        {/* Buttons */}
        <div className="flex flex-col sm:flex-row gap-3 justify-center items-center">
          <button
            onClick={handlePrintReceipt}
            className="bg-blue-500 hover:bg-blue-600 text-white text-lg px-6 py-3 rounded-lg transition-colors duration-200 flex items-center gap-2 w-full sm:w-auto min-w-[180px]"
            disabled={!receiptData}
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Receipt
          </button>
          
          <button
            onClick={handleNewOrder}
            className="btn-primary text-lg px-6 py-3 ripple w-full sm:w-auto min-w-[180px]"
          >
            Place Another Order
          </button>
        </div>
        
        <div className="mt-6 text-sm text-gray-500">
          <p>Your order will be ready shortly!</p>
        </div>
      </div>
    </div>
  );
};

export default OrderComplete;
