<?php

namespace App\CentralLogics;

use App\Model\BusinessSetting;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerLogic{

    public static function loyalty_point_wallet_transfer_transaction($user_id, $point, $amount) {
        // Wallet functionality removed - this method is now disabled
        return false;
    }

    public static function add_to_wallet($customer_id, float $amount)
    {
        // Wallet functionality removed - this method is now disabled
        return false;
    }

    public static function add_to_wallet_bonus($customer_id, $amount)
    {
        // Wallet functionality removed - this method is now disabled
        return 0;
    }

    public static function create_wallet_transaction($user_id, float $amount, $transaction_type, $referance)
    {
        // Wallet functionality removed - this method is now disabled
        return false;
    }

    public static function loyalty_point_transaction($user_id, $point, $transaction_type, $referance)
    {
        // Loyalty point functionality removed - this method is now disabled
        return false;
    }

    public static function loyalty_point_transaction_for_order($user_id, $point, $transaction_type, $referance)
    {
        // Loyalty point functionality removed - this method is now disabled
        return false;
    }
}