# Restaurant Kiosk - Laravel Integration Guide

## Overview
This guide will help you integrate the React-based Restaurant Kiosk app with your Laravel application. The kiosk will run as a React component within Laravel Blade templates.

## Files to Copy to Laravel

### 1. Copy React Source Files
Copy the entire `src/` directory to your Laravel project:
```
Laravel Project Root/
├── resources/
│   └── js/
│       └── kiosk/          # Copy entire src/ folder here
│           ├── components/
│           ├── context/
│           ├── data/
│           ├── services/
│           ├── styles/
│           ├── App.jsx
│           └── main.jsx
```

### 2. Copy Configuration Files
Copy these files to your Laravel project root:
```
Laravel Project Root/
├── package.json            # Merge with existing package.json
├── vite.config.js          # Add to existing vite.config.js
├── tailwind.config.js      # Merge with existing tailwind.config.js
└── postcss.config.js       # Merge with existing postcss.config.js
```

## Laravel Setup Steps

### 1. Install Dependencies
In your Laravel project root, run:
```bash
npm install react react-dom @vitejs/plugin-react
npm install -D tailwindcss autoprefixer postcss
```

### 2. Update Laravel's vite.config.js
Add the kiosk entry point to your existing `vite.config.js`:
```javascript
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/kiosk/main.jsx'  // Add this line
      ],
      refresh: true,
    }),
    react(),  // Add this line
  ],
  build: {
    rollupOptions: {
      output: {
        entryFileNames: 'assets/[name].js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name].[ext]'
      }
    }
  }
})
```

### 3. Update package.json
Merge the kiosk dependencies with your existing `package.json`:
```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "kiosk:dev": "vite --config vite.config.js",
    "kiosk:build": "vite build --config vite.config.js"
  },
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0"
  },
  "devDependencies": {
    "@vitejs/plugin-react": "^4.2.1"
  }
}
```

### 4. Create Laravel Blade Template
Create a new Blade template for the kiosk at `resources/views/kiosk/index.blade.php`:
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restaurant Kiosk</title>
    @vite(['resources/css/app.css', 'resources/js/kiosk/main.jsx'])
</head>
<body class="bg-gray-100">
    <div id="kiosk-app" class="min-h-screen"></div>
</body>
</html>
```

### 5. Create Laravel Route
Add this route to your `routes/web.php`:
```php
Route::get('/kiosk', function () {
    return view('kiosk.index');
})->name('kiosk');
```

### 6. Update Tailwind Config
Merge the kiosk Tailwind config with your existing `tailwind.config.js`:
```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/js/kiosk/**/*.{js,jsx}",  // Add this line
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fef2f2',
          100: '#fee2e2',
          200: '#fecaca',
          300: '#fca5a5',
          400: '#f87171',
          500: '#ef4444',
          600: '#dc2626',
          700: '#b91c1c',
          800: '#991b1b',
          900: '#7f1d1d',
        }
      }
    },
  },
  plugins: [],
}
```

## Integration with Laravel Backend

### 1. Create API Routes
Add these routes to `routes/api.php`:
```php
Route::prefix('kiosk')->group(function () {
    Route::post('/auth/login', [KioskController::class, 'login']);
    Route::post('/auth/logout', [KioskController::class, 'logout']);
    Route::get('/menu', [KioskController::class, 'getMenu']);
    Route::post('/orders', [KioskController::class, 'createOrder']);
});
```

### 2. Create Kiosk Controller
Create `app/Http/Controllers/KioskController.php`:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KioskController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            return response()->json([
                'success' => true,
                'user' => Auth::user()
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Invalid credentials'
        ], 401);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json(['success' => true]);
    }

    public function getMenu(): JsonResponse
    {
        // Return menu data from your database
        return response()->json([
            'categories' => Category::with('products')->get(),
            'products' => Product::with('modifierGroups')->get()
        ]);
    }

    public function createOrder(Request $request): JsonResponse
    {
        // Handle order creation
        $order = Order::create([
            'user_id' => Auth::id(),
            'items' => $request->items,
            'total' => $request->total,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }
}
```

### 3. Update React Auth Service
Update `resources/js/kiosk/services/authService.js` to use Laravel API:
```javascript
const API_BASE = '/api/kiosk';

export const authService = {
  async signIn(email, password) {
    try {
      const response = await fetch(`${API_BASE}/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ email, password })
      });

      const data = await response.json();
      return data;
    } catch (error) {
      return {
        success: false,
        error: 'Network error'
      };
    }
  },

  async signOut() {
    try {
      const response = await fetch(`${API_BASE}/auth/logout`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      });

      return await response.json();
    } catch (error) {
      return { success: false };
    }
  }
};
```

## Building and Running

### Development
```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite for hot reloading
npm run dev
```

### Production
```bash
# Build assets
npm run build

# The built files will be in public/build/
```

## File Structure After Integration

```
Laravel Project/
├── app/
│   └── Http/Controllers/
│       └── KioskController.php
├── resources/
│   ├── js/
│   │   └── kiosk/
│   │       ├── components/
│   │       ├── context/
│   │       ├── data/
│   │       ├── services/
│   │       ├── styles/
│   │       ├── App.jsx
│   │       └── main.jsx
│   └── views/
│       └── kiosk/
│           └── index.blade.php
├── routes/
│   ├── web.php
│   └── api.php
├── public/
│   └── build/          # Generated by Vite
├── package.json
├── vite.config.js
├── tailwind.config.js
└── postcss.config.js
```

## Accessing the Kiosk

Once integrated, you can access the kiosk at:
- Development: `http://localhost:8000/kiosk`
- Production: `https://yourdomain.com/kiosk`

## Next Steps

1. Copy the files as described above
2. Run `npm install` in your Laravel project
3. Update your database migrations to include menu items, categories, and orders
4. Test the integration
5. Customize the styling and functionality as needed

The kiosk will now run as a React component within your Laravel application and can communicate with your Laravel backend through API routes.
