# 🗂️ Pizza N Gyro - Complete Folder Structure Map

## 📁 Root Directory Structure

```
Pizza N Gyro/
├── 📁 app/                          # Main Application Logic
│   ├── 📁 CentralLogics/            # Business Logic Classes
│   │   ├── AddFundHook.php
│   │   ├── banner.php
│   │   ├── category.php
│   │   ├── Constants.php
│   │   ├── coupon.php
│   │   ├── CustomerLogic.php
│   │   ├── helpers.php
│   │   ├── notification.php
│   │   ├── order.php
│   │   ├── product.php
│   │   ├── review.php
│   │   ├── sms_module.php
│   │   └── Translation.php
│   ├── 📁 Console/                  # Artisan Commands
│   │   ├── Commands/
│   │   └── Kernel.php
│   ├── 📁 Exceptions/               # Exception Handling
│   │   └── Handler.php
│   ├── 📁 Http/                     # HTTP Layer
│   │   ├── 📁 Controllers/          # 107 Controller Files
│   │   ├── Kernel.php
│   │   ├── 📁 Middleware/           # 20 Middleware Files
│   │   └── 📁 Resources/            # API Resources
│   ├── 📁 Library/                  # Custom Libraries
│   │   ├── Constant.php
│   │   ├── Payer.php
│   │   ├── Payment.php
│   │   ├── Receiver.php
│   │   └── Responses.php
│   ├── 📁 Mail/                     # Email Classes
│   │   ├── DMSelfRegistration.php
│   │   ├── EmailVerification.php
│   │   ├── OrderPlaced.php
│   │   ├── PasswordResetMail.php
│   │   └── TestEmailSender.php
│   ├── 📁 Model/                    # Eloquent Models (42 files)
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Banner.php
│   │   └── ... (39 more models)
│   ├── 📁 Models/                   # Additional Models
│   │   ├── Cuisine.php
│   │   ├── CuisineProduct.php
│   │   ├── DeliveryChargeByArea.php
│   │   ├── DeliveryChargeSetup.php
│   │   ├── EmailTemplate.php
│   │   ├── GuestUser.php
│   │   ├── LoginSetup.php
│   │   ├── OfflinePayment.php
│   │   ├── OfflinePaymentMethod.php
│   │   ├── OrderArea.php
│   │   ├── OrderChangeAmount.php
│   │   ├── OrderPartialPayment.php
│   │   ├── PaymentRequest.php
│   │   ├── ReferralCustomer.php
│   │   ├── Setting.php
│   │   └── User.php
│   ├── 📁 Observers/                # Model Observers
│   ├── 📁 Providers/                # Service Providers
│   └── 📁 Traits/                   # Reusable Traits
├── 📁 bootstrap/                    # Application Bootstrap
│   ├── app.php
│   └── 📁 cache/
├── 📁 config/                       # Configuration Files
│   ├── app.php
│   ├── auth.php
│   ├── broadcasting.php
│   ├── cache.php
│   ├── constant.php
│   ├── cors.php
│   ├── database.php
│   ├── dompdf.php
│   ├── filesystems.php
│   ├── firebase.php
│   ├── flutterwave.php
│   ├── hashing.php
│   ├── logging.php
│   ├── mail.php
│   ├── modules.php
│   ├── paypal.php
│   ├── paystack.php
│   ├── queue.php
│   ├── razor.php
│   ├── services.php
│   ├── session.php
│   ├── sslcommerz.php
│   └── view.php
├── 📁 database/                     # Database Files
│   ├── addon_settings.sql
│   ├── payment_requests.sql
│   ├── 📁 factories/
│   ├── 📁 migrations/               # 158 Migration Files
│   └── 📁 seeders/
├── 📁 docs/                         # Documentation
│   ├── KDS.md
│   └── REVIEW.md
├── 📁 Modules/                      # Modular Components
│   ├── fi_2544054.svg
│   ├── fi_2544054@2x.png
│   ├── fi_2544054@3x.png
│   └── readme.txt
├── 📁 public/                       # Web Root Directory
│   ├── 📁 assets/                   # Static Assets (719 files)
│   │   ├── 📁 admin/                # Admin Panel Assets
│   │   ├── 📁 customer/             # Customer App Assets
│   │   ├── 📁 delivery-man/         # Delivery App Assets
│   │   ├── 📁 branch/               # Branch App Assets
│   │   └── 📁 chef/                 # Chef App Assets
│   ├── 📁 css/                      # Stylesheets
│   ├── 📁 js/                       # JavaScript Files
│   ├── 📁 Modules/                  # Module Assets
│   ├── 📁 storage/                  # Storage Symlink
│   │   ├── 📁 admin/
│   │   ├── 📁 banner/               # Banner Images
│   │   ├── 📁 branch/
│   │   ├── 📁 category/             # Category Images
│   │   ├── 📁 kitchen/
│   │   ├── 📁 product/              # Product Images
│   │   └── 📁 restaurant/
│   ├── favicon.ico
│   ├── index.php                    # Main Entry Point
│   ├── robots.txt
│   ├── test_images.php              # Image Test Page
│   ├── test_urls.php                # URL Test Page
│   └── web.config
├── 📁 resources/                    # Resource Files
│   ├── 📁 js/                       # JavaScript Source
│   ├── 📁 lang/                     # Language Files (13 languages)
│   └── 📁 views/                    # Blade Templates (285 files)
│       ├── 📁 admin/                # Admin Views
│       ├── 📁 branch/               # Branch Views
│       ├── 📁 chef/                 # Chef Views
│       ├── 📁 customer/             # Customer Views
│       ├── 📁 delivery-man/         # Delivery Views
│       └── 📁 email/                # Email Templates
├── 📁 routes/                       # Route Definitions
│   ├── admin.php                    # Admin Routes
│   ├── api/                         # API Routes
│   ├── branch.php                   # Branch Routes
│   ├── chef.php                     # Chef Routes
│   ├── channels.php                 # Broadcasting Routes
│   ├── console.php                  # Console Routes
│   ├── update.php                   # Update Routes
│   └── web.php                      # Web Routes
├── 📁 storage/                      # Storage Directory
│   ├── 📁 app/                      # Application Storage
│   │   └── 📁 public/               # Public Storage
│   │       ├── 📁 admin/
│   │       ├── 📁 banner/           # Banner Images Storage
│   │       ├── 📁 branch/
│   │       ├── 📁 category/         # Category Images Storage
│   │       ├── 📁 kitchen/
│   │       ├── 📁 product/          # Product Images Storage
│   │       └── 📁 restaurant/
│   ├── 📁 debugbar/                 # Debug Bar Storage
│   ├── 📁 fonts/                    # Font Storage
│   ├── 📁 framework/                # Framework Storage
│   ├── 📁 logs/                     # Log Files
│   └── 📁 tmp/                      # Temporary Files
├── 📁 stubs/                        # Code Stubs
├── 📁 tests/                        # Test Files
│   ├── CreatesApplication.php
│   ├── 📁 Feature/                  # Feature Tests
│   ├── TestCase.php
│   └── 📁 Unit/                     # Unit Tests
├── 📁 vendor/                       # Composer Dependencies
├── 📄 artisan                       # Artisan Command Line Tool
├── 📄 auto_builder.sh               # Build Script
├── 📄 composer.json                 # Composer Configuration
├── 📄 composer.lock                 # Composer Lock File
├── 📄 composer.phar                 # Composer Executable
├── 📄 error_log                     # Error Log
├── 📄 firebase-messaging-sw.js      # Firebase Service Worker
├── 📄 hnd804_food.sql               # Database Dump
├── 📄 IMAGES_DEPLOYMENT_COMPLETE.md # Image Deployment Documentation
├── 📄 index.php                     # Application Entry Point
├── 📄 modules_statuses.json         # Module Status
├── 📄 package.json                  # NPM Configuration
├── 📄 package-lock.json             # NPM Lock File
├── 📄 php.ini                       # PHP Configuration
├── 📄 phpunit.xml                   # PHPUnit Configuration
├── 📄 README.md                     # Project Documentation
├── 📄 server.php                    # Server Configuration
├── 📄 setup_database_with_images.sql # Database Setup Script
├── 📄 SETUP_INSTRUCTIONS.md         # Setup Instructions
├── 📄 update_images_script.php      # Image Update Script
├── 📄 update_images.sql             # Image Update SQL
└── 📄 webpack.mix.js                # Webpack Mix Configuration
```

## 🎯 Key Directories Explained

### 📱 **Multi-App Architecture**
This Laravel application supports multiple user interfaces:
- **Admin Panel** (`/admin`) - Restaurant management
- **Customer App** (`/customer`) - Customer ordering interface
- **Branch App** (`/branch`) - Branch management
- **Chef App** (`/chef`) - Kitchen management
- **Delivery App** (`/delivery-man`) - Delivery management

### 🗄️ **Database & Storage**
- **Database**: MySQL/MariaDB with comprehensive food delivery schema
- **Storage**: Laravel's storage system with organized image directories
- **Images**: Properly categorized and stored for products, categories, banners

### 🔧 **Configuration & Setup**
- **Config**: Extensive configuration for payments, SMS, email, etc.
- **Routes**: Separate route files for different app sections
- **Views**: Blade templates organized by user type

### 📦 **Dependencies & Modules**
- **Composer**: PHP dependencies including payment gateways
- **NPM**: Frontend build tools
- **Modules**: Modular architecture for extensibility

### 🚀 **Deployment & Testing**
- **Artisan**: Laravel command-line interface
- **Tests**: Feature and unit tests
- **Build Scripts**: Automated build and deployment tools

## 📊 **File Count Summary**
- **Controllers**: 107 files
- **Models**: 42 files (app/Model) + 16 files (app/Models)
- **Views**: 285 files
- **Migrations**: 158 files
- **Languages**: 13 language files
- **Assets**: 719 files (images, CSS, JS)
- **Routes**: 7 route files

## 🔗 **Image Storage Structure**
```
storage/app/public/
├── product/          # Product images (11 images)
├── category/         # Category images (1 image)
├── banner/           # Banner images (2 images)
├── admin/            # Admin panel images
├── branch/           # Branch images
├── kitchen/          # Kitchen images
└── restaurant/       # Restaurant images
```

This is a comprehensive food delivery application with multi-tenant architecture, supporting restaurants, customers, delivery personnel, and kitchen staff with a complete management system.
