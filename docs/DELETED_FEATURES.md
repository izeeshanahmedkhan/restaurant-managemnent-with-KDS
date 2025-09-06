# Deleted Features Documentation

## Overview
This document provides a comprehensive record of all features, modules, and functionality that have been removed from the Pizza N Gyro restaurant management system to create a streamlined, English-only platform focused on core restaurant operations.

## Deleted Features Summary

### 1. **Subscribed Email System**
- **Purpose**: Email subscription management for customers
- **Files Removed**:
  - `app/Http/Controllers/Admin/SubscribedEmailController.php`
  - `app/Model/SubscribedEmail.php`
  - `database/migrations/2020_12_28_092747_create_subscribed_emails_table.php`
  - `resources/views/admin-views/subscribed-email/`
- **Routes Removed**: All email subscription routes from `routes/admin.php`
- **Impact**: No email subscription functionality

### 2. **Cuisine Management System**
- **Purpose**: Cuisine categorization and management
- **Files Removed**:
  - `app/Models/Cuisine.php`
  - `app/Models/CuisineProduct.php`
  - `app/Http/Controllers/Admin/CuisineController.php`
  - `database/migrations/2020_12_28_092747_create_cuisines_table.php`
  - `database/migrations/2020_12_28_092747_create_cuisine_products_table.php`
  - `resources/views/admin-views/cuisine/`
- **Routes Removed**: All cuisine-related routes
- **Impact**: No cuisine categorization functionality

### 3. **Coupon System**
- **Purpose**: Discount coupon management and application
- **Files Removed**:
  - `app/Model/Coupon.php`
  - `app/Http/Controllers/Admin/CouponController.php`
  - `app/Http/Controllers/Api/V1/CouponController.php`
  - `app/CentralLogics/coupon.php`
  - `database/migrations/2020_12_28_092747_create_coupons_table.php`
  - `resources/views/admin-views/coupon/`
- **Routes Removed**: All coupon-related routes
- **Impact**: No discount coupon functionality

### 4. **Messages/Conversation System**
- **Purpose**: Customer support messaging system
- **Files Removed**:
  - `app/Model/Conversation.php`
  - `app/Model/Message.php`
  - `app/Http/Controllers/Admin/ConversationController.php`
  - `app/Http/Controllers/Api/V1/ConversationController.php`
  - `database/migrations/2020_12_28_092747_create_conversations_table.php`
  - `database/migrations/2020_12_28_092747_create_messages_table.php`
  - `resources/views/admin-views/conversation/`
- **Routes Removed**: All messaging routes
- **Impact**: No customer support messaging functionality

### 5. **Table Management System**
- **Purpose**: Dine-in table management and reservations
- **Files Removed**:
  - `app/Model/TableOrder.php`
  - `app/Http/Controllers/Admin/TableOrderController.php`
  - `app/Http/Controllers/Branch/TableOrderController.php`
  - `database/migrations/2020_12_28_092747_create_table_orders_table.php`
  - `resources/views/admin-views/table-order/`
  - `resources/views/branch-views/table-order/`
- **Routes Removed**: All table management routes
- **UI Removed**: Dine-in section from POS interface
- **Impact**: No table management or dine-in functionality

### 6. **System Addon Management**
- **Purpose**: System-wide addon management
- **Files Removed**:
  - `app/Http/Controllers/Admin/SystemAddonController.php`
  - `resources/views/admin-views/system-addon/`
- **Routes Removed**: All system addon routes
- **Impact**: No system addon management

### 7. **Email Template System**
- **Purpose**: Customizable email templates
- **Files Removed**:
  - `app/Models/EmailTemplate.php`
  - `app/Http/Controllers/Admin/EmailTemplateController.php`
  - `database/migrations/2020_12_28_092747_create_email_templates_table.php`
  - `resources/views/admin-views/email-template/`
- **Routes Removed**: All email template routes
- **Impact**: No custom email template functionality

### 8. **SMS/Message Sending System**
- **Purpose**: SMS notification and messaging
- **Files Removed**:
  - `app/CentralLogics/sms_module.php`
  - SMS-related functionality from controllers
- **Impact**: No SMS messaging capability

### 9. **Customer Wallet System**
- **Purpose**: Digital wallet for customers
- **Files Removed**:
  - `app/Model/WalletTransaction.php`
  - `app/Http/Controllers/Admin/WalletController.php`
  - `app/Http/Controllers/Api/V1/WalletController.php`
  - `database/migrations/2020_12_28_092747_create_wallet_transactions_table.php`
  - `resources/views/admin-views/wallet/`
- **Routes Removed**: All wallet-related routes
- **Impact**: No customer wallet functionality

### 10. **Customer Loyalty Points System**
- **Purpose**: Loyalty points and rewards program
- **Files Removed**:
  - `app/Model/LoyaltyPointTransaction.php`
  - `app/Http/Controllers/Admin/LoyaltyPointController.php`
  - `app/Http/Controllers/Api/V1/LoyaltyPointController.php`
  - `database/migrations/2020_12_28_092747_create_loyalty_point_transactions_table.php`
  - `resources/views/admin-views/loyalty-point/`
- **Routes Removed**: All loyalty point routes
- **Impact**: No loyalty points or rewards system

### 11. **Delivery Man Management System**
- **Purpose**: Delivery personnel management and tracking
- **Files Removed**:
  - `app/Model/DeliveryMan.php`
  - `app/Http/Controllers/Admin/DeliveryManController.php`
  - `app/Http/Controllers/Api/V1/DeliveryManController.php`
  - `database/migrations/2020_12_28_092747_create_delivery_men_table.php`
  - `resources/views/admin-views/delivery-man/`
- **Routes Removed**: All delivery man routes
- **Middleware Removed**: `deliveryman_is_active` middleware
- **Impact**: No delivery management functionality

### 12. **3rd Party Payment Gateways**
- **Purpose**: External payment integrations
- **Files Removed**:
  - `app/Http/Controllers/PaymobController.php`
  - Various payment gateway integrations
- **Routes Removed**: All 3rd party payment routes
- **Impact**: No external payment gateway support

### 13. **Notification System**
- **Purpose**: Push notifications and alerts
- **Files Removed**:
  - `app/Models/Notification.php`
  - `app/Http/Controllers/Admin/NotificationController.php`
  - `app/Http/Controllers/Api/V1/NotificationController.php`
  - `database/migrations/2020_12_28_092747_create_notifications_table.php`
  - `resources/views/admin-views/notification/`
- **Routes Removed**: All notification routes
- **Impact**: No push notification system

### 14. **Banner Management System**
- **Purpose**: Promotional banner management
- **Files Removed**:
  - `app/Model/Banner.php`
  - `app/Http/Controllers/Admin/BannerController.php`
  - `app/CentralLogics/banner.php`
  - `app/Http/Controllers/Api/V1/BannerController.php`
  - `database/migrations/2020_12_28_092607_create_banners_table.php`
  - `database/migrations/2021_01_23_102525_add_col_to_banner_category_id.php`
  - `database/migrations/2022_02_10_163707_change_banner_title_length.php`
  - `resources/views/admin-views/banner/`
- **Routes Removed**: All banner routes
- **Impact**: No banner management functionality

### 15. **3rd Party Integration Options**
- **Purpose**: External service integrations
- **Files Removed**:
  - `resources/views/admin-views/business-settings/partials/_3rdparty-inline-menu.blade.php`
  - `resources/views/admin-views/business-settings/social-media.blade.php`
  - `resources/views/admin-views/business-settings/social-login.blade.php`
  - `resources/views/admin-views/business-settings/recaptcha-index.blade.php`
  - `resources/views/admin-views/business-settings/fcm-config.blade.php`
  - `resources/views/admin-views/business-settings/fcm-index.blade.php`
  - `resources/views/admin-views/business-settings/marketing-tools.blade.php`
- **Routes Removed**: All 3rd party integration routes
- **Impact**: No external service integrations

### 16. **Offline Payment System**
- **Purpose**: Offline payment method management
- **Files Removed**:
  - `app/Models/OfflinePayment.php`
  - `app/Models/OfflinePaymentMethod.php`
  - `app/Http/Controllers/Admin/OfflinePaymentMethodController.php`
  - `database/migrations/2023_08_06_121550_create_offline_payments_table.php`
  - `database/migrations/2023_11_01_124210_add_status_column_in_offline_payments.php`
  - `database/migrations/2023_08_01_164756_create_offline_payment_methods_table.php`
  - `resources/views/admin-views/business-settings/offline-payment/`
  - `resources/views/branch-views/order/offline-payment/`
- **Routes Removed**: All offline payment routes
- **Impact**: No offline payment functionality

### 17. **Multi-Language System**
- **Purpose**: Internationalization and localization
- **Files Modified**:
  - `app/CentralLogics/helpers.php` - Modified `translate()` function to always use English
  - `app/CentralLogics/Translation.php` - Modified to always use English
  - All controllers - Removed translation logic
  - All models - Removed translation relationships
- **Routes Removed**: All language-related routes
- **UI Removed**: Language selector from branch header
- **Impact**: English-only interface

### 18. **System Settings**
- **Purpose**: Advanced system configuration
- **Files Removed**:
  - `resources/views/admin-views/business-settings/partials/_system-settings-inline-menu.blade.php`
- **Routes Removed**: All system setup routes
- **Impact**: No advanced system configuration

### 19. **Android/iOS App Relations**
- **Purpose**: Mobile app integration and management
- **Files Modified**:
  - `routes/admin.php` - Removed `app_activate` middleware
  - `routes/api/v1/api.php` - Removed `app_activate` middleware
  - `routes/branch.php` - Removed `app_activate` middleware
- **Impact**: No mobile app integration

### 20. **Product Import/Export System**
- **Purpose**: Bulk product import and export functionality
- **Files Removed**:
  - `app/Http/Controllers/Admin/ReviewsController.php`
  - `resources/views/admin-views/product/bulk-import.blade.php`
  - `resources/views/admin-views/product/bulk-export.blade.php`
- **Methods Removed**:
  - `ProductController::bulkImportIndex()`
  - `ProductController::bulkImportData()`
  - `ProductController::bulkExportData()`
  - `ProductController::bulkExportIndex()`
  - `ProductController::excelImport()`
- **Routes Removed**: All bulk import/export routes
- **Impact**: No bulk product import/export functionality

### 21. **Product Reviews System**
- **Purpose**: Customer product reviews and ratings
- **Files Removed**:
  - `app/Model/Review.php`
  - `app/CentralLogics/review.php`
  - `database/migrations/2020_12_28_093812_create_reviews_table.php`
  - `database/migrations/2021_02_15_094227_add_orderid_to_reviews_table.php`
  - `resources/views/admin-views/reviews/list.blade.php`
- **Methods Removed**:
  - `ProductController::productReviews()`
  - `ProductController::productRating()`
  - `ProductController::submitProductReview()`
- **Routes Removed**: All review-related routes
- **Impact**: No product review or rating system

## Code Modifications Made

### Controllers Modified
- **Admin Controllers**:
  - `POSController.php` - Removed table, delivery man, notification references
  - `OrderController.php` - Removed delivery man, loyalty, wallet, offline payment references
  - `BusinessSettingsController.php` - Removed wallet, loyalty, notification, translation logic
  - `CategoryController.php` - Removed translation logic
  - `ProductController.php` - Removed translation logic, import/export, review functionality
  - `AddonController.php` - Removed translation logic
  - `AttributeController.php` - Removed translation logic
  - `CustomerController.php` - Removed loyalty point functionality

- **API Controllers**:
  - `OrderController.php` - Removed delivery man, offline payment references
  - `ProductController.php` - Removed translation logic, review functionality
  - `ConfigController.php` - Removed delivery man, banner, review references

- **Branch Controllers**:
  - `POSController.php` - Removed table, delivery man, notification references
  - `OrderController.php` - Removed loyalty, wallet, offline payment references

### Models Modified
- **Order.php** - Removed delivery man, table order, offline payment relationships
- **Product.php** - Removed translation relationships
- **Category.php** - Removed translation relationships
- **AddOn.php** - Removed translation relationships
- **Attribute.php** - Removed translation relationships
- **BusinessSetting.php** - Removed translation relationships

### Views Modified
- **Admin Views**:
  - `pos/index.blade.php` - Removed dine-in section
  - `order/order-view.blade.php` - Removed delivery man, cutlery options
  - `layouts/admin/partials/_sidebar.blade.php` - Removed deleted feature menu items

- **Branch Views**:
  - `pos/index.blade.php` - Removed dine-in section
  - `order/order-view.blade.php` - Removed delivery man, cutlery options
  - `layouts/branch/partials/_sidebar.blade.php` - Removed table management menu
  - `layouts/branch/partials/_header.blade.php` - Removed language selector

### Routes Modified
- **admin.php** - Removed all deleted feature routes
- **api/v1/api.php** - Removed all deleted feature routes
- **branch.php** - Removed all deleted feature routes
- **chef.php** - Removed app_activate middleware

### Helper Functions Modified
- **helpers.php** - Modified `translate()` function to always use English
- **Translation.php** - Modified to always use English
- Removed delivery man, loyalty, wallet related functions

## Database Changes

### Tables Removed
- `subscribed_emails`
- `cuisines`
- `cuisine_products`
- `coupons`
- `conversations`
- `messages`
- `table_orders`
- `email_templates`
- `wallet_transactions`
- `loyalty_point_transactions`
- `delivery_men`
- `notifications`
- `banners`
- `offline_payments`
- `offline_payment_methods`
- `reviews`
- `d_m_reviews`

### Columns Removed
- `delivery_man_id` from `orders` table
- `delivery_man_id` from `order_transactions` table
- `table_id` from `orders` table
- `number_of_people` from `orders` table
- `table_order_id` from `orders` table
- `is_cutlery_required` from `orders` table

## Current System Features

### Remaining Core Features
1. **POS System** - Point of sale functionality
2. **Product Management** - Product and category management
3. **Order Management** - Order processing and tracking
4. **Branch Management** - Multi-branch support
5. **Kitchen Display System** - Kitchen order management
6. **Basic Business Settings** - Essential configuration
7. **User Management** - Admin and staff management
8. **Customer Management** - Customer data management
9. **Order Analytics** - Basic reporting and statistics

### System Characteristics
- **Language**: English only
- **Payment Methods**: Cash and basic online payments only
- **Order Types**: POS and home delivery only (no dine-in)
- **Notifications**: Basic system notifications only
- **Interface**: Streamlined, focused on core operations

## Technical Notes

### Error Fixes Applied
1. **Translation Errors**: Fixed all `count()` errors with null translations
2. **Model Errors**: Removed all references to deleted models
3. **Route Errors**: Removed all references to deleted routes
4. **Controller Errors**: Removed all references to deleted controllers
5. **View Errors**: Removed all references to deleted UI elements

### Performance Impact
- **Reduced Database Queries**: Fewer tables and relationships
- **Simplified Codebase**: Cleaner, more maintainable code
- **Faster Loading**: Removed unnecessary features and dependencies
- **Lower Memory Usage**: Fewer models and controllers loaded

## Maintenance Notes

### Future Development
- All deleted features can be re-implemented if needed
- Database migrations are preserved for reference
- Code structure remains intact for easy feature addition
- Translation system can be re-enabled by restoring relationships

### Backup Recommendations
- Keep this documentation updated
- Maintain database backups before major changes
- Document any new features added
- Keep track of any custom modifications

---

**Document Created**: December 2024  
**System Version**: Pizza N Gyro v1.0 (Streamlined)  
**Last Updated**: December 2024
