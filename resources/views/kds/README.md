# Kitchen Display System (KDS) - Documentation

## Overview

The Kitchen Display System (KDS) is a real-time order management interface designed for restaurant kitchens. It provides a clean, accessible, and performant way to manage orders across different statuses with live updates.

## Features

- **Real-time Updates**: Polls server every 4 seconds for order changes
- **Three-Column Layout**: New Orders, Cooking, and Done columns
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Accessibility**: WCAG AA compliant with keyboard navigation
- **Search & Filter**: Search orders by number, customer, or items
- **Branch Management**: Multi-branch support with role-based access
- **Audio Notifications**: Optional sound alerts for new orders
- **Auto-hide**: Done orders automatically hide after 2 hours

## File Structure

```
resources/views/kds/
├── index.blade.php          # Main KDS dashboard template
├── _order-card.blade.php    # Order card partial component
└── README.md               # This documentation

public/css/
└── kds.css                 # KDS-specific styles with CSS variables

public/js/
└── kds.js                  # jQuery-based KDS functionality
```

## API Endpoints

### GET /admin/kds/orders

Fetches orders with optional filtering.

**Parameters:**
- `since` (string, optional): ISO timestamp for incremental updates
- `branch_id` (integer, required): Branch ID to filter orders
- `search` (string, optional): Search query for orders

**Response:**
```json
{
  "now": "2025-09-05T03:23:11Z",
  "orders": [
    {
      "id": 19,
      "number": "0000019",
      "status": "NEW",
      "placed_at": "2025-09-05T03:46:00Z",
      "items": [
        {
          "name": "Beef Spicy Burger",
          "quantity": 2,
          "size": "Regular",
          "variations": []
        }
      ],
      "token": "A123",
      "customer_name": "John Doe",
      "total_amount": 25.99,
      "order_note": "Extra spicy"
    }
  ]
}
```

### PUT /admin/kds/orders/{id}/status

Updates order status.

**Request Body:**
```json
{
  "status": "COOKING",
  "_token": "csrf_token_here"
}
```

**Response:**
```json
{
  "ok": true
}
```

## Status Flow

Orders progress through these statuses:

1. **NEW/PENDING/CONFIRMED/PROCESSING** → New Orders column
2. **COOKING** → Cooking column  
3. **DONE/COMPLETED** → Done column (auto-hide after 2 hours)

## CSS Variables

The design system uses CSS variables for easy customization:

```css
:root {
  /* Color System */
  --bg: #0F172A;                    /* Background */
  --panel: #111827;                 /* Panel background */
  --card: #0B1220;                  /* Card background */
  --text: #E5E7EB;                  /* Primary text */
  --muted: #9CA3AF;                 /* Muted text */
  
  /* Status Colors */
  --kds-new: #3B82F6;               /* Blue for new orders */
  --kds-cooking: #F59E0B;           /* Orange for cooking */
  --kds-done: #10B981;              /* Green for done */
  
  /* Design Tokens */
  --radius: 14px;                   /* Border radius */
  --gap: 16px;                      /* Spacing */
  --shadow: 0 6px 20px rgba(0,0,0,0.25); /* Box shadow */
}
```

## Customization

### Changing Colors

To change the color scheme, update the CSS variables in `public/css/kds.css`:

```css
:root {
  --kds-new: #your-color-here;
  --kds-cooking: #your-color-here;
  --kds-done: #your-color-here;
}
```

### Adjusting Polling Interval

Modify the polling interval in `public/js/kds.js`:

```javascript
const CONFIG = {
    pollInterval: 4000, // Change to desired milliseconds
    // ... other config
};
```

### Disabling Audio Notifications

Set `soundEnabled` to `false` in the config:

```javascript
const CONFIG = {
    soundEnabled: false,
    // ... other config
};
```

## Browser Support

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## Accessibility Features

- **Keyboard Navigation**: Full keyboard support for all interactive elements
- **ARIA Labels**: Proper labeling for screen readers
- **Focus Management**: Clear focus indicators
- **High Contrast**: Support for high contrast mode
- **Reduced Motion**: Respects user's motion preferences

## Performance Considerations

- **Efficient Polling**: Only fetches changed orders using `since` parameter
- **DOM Optimization**: Minimal DOM manipulation for updates
- **Lazy Loading**: Images and heavy content loaded on demand
- **Memory Management**: Proper cleanup of event listeners and intervals

## Troubleshooting

### Common Issues

1. **Orders not updating**: Check browser console for API errors
2. **Status changes not working**: Verify CSRF token and user permissions
3. **Audio not playing**: Check browser audio permissions
4. **Layout issues**: Ensure CSS variables are properly loaded

### Debug Mode

Enable debug logging by opening browser console. All KDS operations are logged with `KDS:` prefix.

## Security

- **CSRF Protection**: All state-changing requests include CSRF tokens
- **Role-based Access**: Users can only access assigned branches
- **Input Validation**: All inputs are validated server-side
- **XSS Prevention**: All output is properly escaped

## Integration

### With Existing Laravel App

1. Include the KDS routes in your admin routes
2. Ensure proper middleware is applied
3. Update user permissions for branch access
4. Customize the design to match your brand

### With External Systems

The KDS can be integrated with external POS systems by:

1. Creating webhook endpoints for order updates
2. Implementing custom order status mappings
3. Adding custom notification channels

## Support

For technical support or feature requests, please refer to the main project documentation or contact the development team.

---

**Version**: 1.0.0  
**Last Updated**: September 2025  
**Compatibility**: Laravel 8+, PHP 8.0+
