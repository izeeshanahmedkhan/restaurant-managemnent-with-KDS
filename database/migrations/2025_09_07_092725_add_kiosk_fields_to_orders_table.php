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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('kiosk_id')->nullable()->after('branch_id');
            $table->unsignedBigInteger('kiosk_user_id')->nullable()->after('kiosk_id');
            $table->enum('order_source', ['pos', 'kiosk', 'online'])->default('pos')->after('kiosk_user_id');
            
            $table->foreign('kiosk_id')->references('id')->on('kiosks')->onDelete('set null');
            $table->foreign('kiosk_user_id')->references('id')->on('kiosk_users')->onDelete('set null');
            $table->index(['order_source', 'kiosk_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['kiosk_id']);
            $table->dropForeign(['kiosk_user_id']);
            $table->dropIndex(['order_source', 'kiosk_id']);
            $table->dropColumn(['kiosk_id', 'kiosk_user_id', 'order_source']);
        });
    }
};
