<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop tables for removed features
        Schema::dropIfExists('delivery_men');
        Schema::dropIfExists('d_m_reviews');
        Schema::dropIfExists('delivery_histories');
        Schema::dropIfExists('order_delivery_histories');
        Schema::dropIfExists('track_deliverymen');
        Schema::dropIfExists('point_transitions');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallet_bonuses');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('table_orders');
        Schema::dropIfExists('cuisines');
        Schema::dropIfExists('cuisine_products');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('addon_settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only drops tables, so we don't need to reverse it
        // The original table creation migrations were already deleted
    }
};
