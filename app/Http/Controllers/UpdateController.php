<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\AddOn;
use App\Model\Admin;
use App\Model\AdminRole;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Models\LoginSetup;
use App\Traits\ActivationClass;
use App\User;
use App\Models\DeliveryChargeSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Brian2694\Toastr\Facades\Toastr;


class UpdateController extends Controller
{
    use ActivationClass;
    public function update_software_index()
    {
        return view('update.update-software');
    }

    public function update_software(Request $request)
    {
        Helpers::setEnvironmentValue('SOFTWARE_ID', 'MzAzMjAzMzg=');
        Helpers::setEnvironmentValue('BUYER_USERNAME', $request['username']);
        Helpers::setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);
        Helpers::setEnvironmentValue('APP_MODE', 'live');
        Helpers::setEnvironmentValue('SOFTWARE_VERSION', '11.5');
        Helpers::setEnvironmentValue('APP_NAME', 'efood');

        $data = $this->actch();

        try {
            if (!$data->getData()->active) {
                $remove = array("http://", "https://", "www.");
                $url = str_replace($remove, "", url('/'));
                $activation_url = base64_decode('aHR0cHM6Ly9hY3RpdmF0aW9uLjZhbXRlY2guY29t');
                $activation_url .= '?username=' . $request['username'];
                $activation_url .= '&purchase_code=' . $request['purchase_key'];
                $activation_url .= '&domain=' . $url . '&';
                return redirect($activation_url);
            }
        } catch (\Exception $exception) {
            Toastr::error('verification failed! try again');
            return back();
        }

        Artisan::call('migrate', ['--force' => true]);

        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);

        Artisan::call('optimize:clear');


        DB::table('business_settings')->updateOrInsert(['key' => 'self_pickup'], [
            'value' => 1
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'delivery'], [
            'value' => 1
        ]);

        if (BusinessSetting::where(['key' => 'paystack'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'paystack',
                'value' => '{"status":"1","publicKey":"","razor_secret":"","secretKey":"","paymentUrl":"","merchantEmail":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'senang_pay'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'senang_pay',
                'value' => '{"status":"1","secret_key":"","merchant_id":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'bkash'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'bkash',
                'value' => '{"status":"1","api_key":"","api_secret":"","username":"","password":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'paymob'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'paymob',
                'value' => '{"status":"1","api_key":"","iframe_id":"","integration_id":"","hmac":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'flutterwave'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'flutterwave',
                'value' => '{"status":"1","public_key":"","secret_key":"","hash":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'mercadopago'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'mercadopago',
                'value' => '{"status":"1","public_key":"","access_token":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'paypal'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'paypal',
                'value' => '{"status":"1","paypal_client_id":"","paypal_secret":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'internal_point'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'internal_point',
                'value' => '{"status":"1"}'
            ]);
        }
        Order::where('delivery_date', null)->update([
            'delivery_date' => date('y-m-d', strtotime("-1 days")),
            'delivery_time' => '12:00',
            'updated_at' => now()
        ]);

        if (BusinessSetting::where(['key' => 'language'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'language'], [
                'value' => json_encode(["en"])
            ]);
        }
        if (BusinessSetting::where(['key' => 'time_zone'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'time_zone'], [
                'value' => 'Pacific/Midway'
            ]);
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'phone_verification'], [
            'value' => 0
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'msg91_sms'], [
            'key' => 'msg91_sms',
            'value' => '{"status":0,"template_id":null,"authkey":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => '2factor_sms'], [
            'key' => '2factor_sms',
            'value' => '{"status":"0","api_key":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'nexmo_sms'], [
            'key' => 'nexmo_sms',
            'value' => '{"status":0,"api_key":null,"api_secret":null,"signature_secret":"","private_key":"","application_id":"","from":null,"otp_template":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'twilio_sms'], [
            'key' => 'twilio_sms',
            'value' => '{"status":0,"sid":null,"token":null,"from":null,"otp_template":null}'
        ]);
        if (BusinessSetting::where(['key' => 'pagination_limit'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'pagination_limit'], [
                'value' => 10
            ]);
        }
        if (BusinessSetting::where(['key' => 'default_preparation_time'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'default_preparation_time'], [
                'value' => 30
            ]);
        }
        if(BusinessSetting::where(['key' => 'decimal_point_settings'])->first() == false)
        {
            DB::table('business_settings')->updateOrInsert(['key' => 'decimal_point_settings'], [
                'value' => 2
            ]);
        }
        if (BusinessSetting::where(['key' => 'map_api_key'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'map_api_key'], [
                'value' => ''
            ]);
        }

        if (BusinessSetting::where(['key' => 'play_store_config'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'play_store_config'], [
                'value' => '{"status":"","link":"","min_version":""}'
            ]);
        } else {
            $play_store_config = Helpers::get_business_settings('play_store_config');
            DB::table('business_settings')->updateOrInsert(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $play_store_config['status'],
                    'link' => $play_store_config['link'],
                    'min_version' => "1",
                ])
            ]);
        }

        if (BusinessSetting::where(['key' => 'app_store_config'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'app_store_config'], [
                'value' => '{"status":"","link":"","min_version":""}'
            ]);
        } else {
            $app_store_config = Helpers::get_business_settings('app_store_config');
            DB::table('business_settings')->updateOrInsert(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $app_store_config['status'],
                    'link' => $app_store_config['link'],
                    'min_version' => "1",
                ])
            ]);
        }

        // Delivery man functionality removed
        if (BusinessSetting::where(['key' => 'recaptcha'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'recaptcha'], [
                'value' => '{"status":"0","site_key":"","secret_key":""}'
            ]);
        }


        //for modified language [new multi lang in admin]
        $languages = Helpers::get_business_settings('language');
        $lang_array = [];
        $lang_flag = false;

        foreach ($languages as $key => $language) {
            if(gettype($language) != 'array') {
                $lang = [
                    'id' => $key+1,
                    'name' => $language,
                    'direction' => 'ltr',
                    'code' => $language,
                    'status' => 1,
                    'default' => $language == 'en' ? true : false,
                ];

                array_push($lang_array, $lang);
                $lang_flag = true;
            }
        }
        if ($lang_flag == true) {
            BusinessSetting::where('key', 'language')->update([
                'value' => $lang_array
            ]);
        }
        //lang end

        if (BusinessSetting::where(['key' => 'schedule_order_slot_duration'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'schedule_order_slot_duration'], [
                'value' => '1'
            ]);
        }

        if (BusinessSetting::where(['key' => 'time_format'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'time_format'], [
                'value' => '24'
            ]);
        }

        //for role management
        $admin_role = AdminRole::get()->first();
        if (!$admin_role) {
            DB::table('admin_roles')->insertOrIgnore([
                'id' => 1,
                'name' => 'Master Admin',
                'module_access' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $admin = Admin::get()->first();
        if($admin) {
            $admin->admin_role_id = 1;
            $admin->save();
        }

        $mail_config = \App\CentralLogics\Helpers::get_business_settings('mail_config');
        BusinessSetting::where(['key' => 'mail_config'])->update([
            'value' => json_encode([
                "status" => 0,
                "name" => $mail_config['name']??'',
                "host" => $mail_config['host']??'',
                "driver" => $mail_config['driver']??'',
                "port" => $mail_config['port']??'',
                "username" => $mail_config['username']??'',
                "email_id" => $mail_config['email_id']??'',
                "encryption" => $mail_config['encryption']??'',
                "password" => $mail_config['password']??''
            ]),
        ]);

        //*** auto run script ***
        try {
            $order_details = OrderDetail::get();
            foreach($order_details as $order_detail) {

                //*** addon quantity integer casting script ***
                $qtys = json_decode($order_detail['add_on_qtys'], true);
                array_walk($qtys, function (&$add_on_qtys) {
                    $add_on_qtys = (int) $add_on_qtys;
                });
                $order_detail['add_on_qtys'] = json_encode($qtys);
                //*** end ***


                //*** variation(POS) structure change script ***
                $variation = json_decode($order_detail['variation'], true);
                $product = json_decode($order_detail['product_details'], true);

                if(count($variation) > 0) {
                    $result = [];
                    if(!array_key_exists('price', $variation[0])) {
                        $result[] = [
                            'type' => $variation[0]['Size'],
                            'price' => Helpers::set_price($product['price'])
                        ];
                    }
                    if(count($result) > 0) {
                        $order_detail['variation'] = json_encode($result);
                    }

                }
                //*** end ***

                $order_detail->save();


            }
        } catch (\Exception $exception) {
            //
        }
        //*** end ***

        DB::table('branches')->insertOrIgnore([
            'id' => 1,
            'name' => 'Main Branch',
            'email' => 'main@gmail.com',
            'password' => '',
            'coverage' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if (!BusinessSetting::where(['key' => 'wallet_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'wallet_status'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'loyalty_point_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'loyalty_point_status'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'ref_earning_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'ref_earning_status'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'loyalty_point_exchange_rate'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'loyalty_point_exchange_rate'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'ref_earning_exchange_rate'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'ref_earning_exchange_rate'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'loyalty_point_item_purchase_point'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'loyalty_point_item_purchase_point'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'loyalty_point_minimum_point'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'loyalty_point_minimum_point'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'whatsapp'])->first()) {
            BusinessSetting::insert([
                'key' => 'whatsapp',
                'value' => '{"status":0,"number":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'fav_icon'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'fav_icon'], [
                'value' => ''
            ]);
        }

        //user referral code
        $users = User::whereNull('refer_code')->get();
        foreach ($users as $user) {
            $user->refer_code = Helpers::generate_referer_code();
            $user->save();
        }

        if (!BusinessSetting::where(['key' => 'cookies'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'cookies'], [
                'value' => '{"status":"1","text":"Allow Cookies for this site"}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'maximum_otp_hit'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maximum_otp_hit'], [
                'value' => 5
            ]);
        }

        if (!BusinessSetting::where(['key' => 'otp_resend_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'otp_resend_time'], [
                'value' => 60
            ]);
        }

        if (!BusinessSetting::where(['key' => 'temporary_block_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'temporary_block_time'], [
                'value' => 600
            ]);
        }

        if (!BusinessSetting::where(['key' => 'dm_self_registration'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'dm_self_registration'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'toggle_veg_non_veg'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'toggle_veg_non_veg'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'maximum_login_hit'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maximum_login_hit'], [
                'value' => 5
            ]);
        }

        if (!BusinessSetting::where(['key' => 'temporary_login_block_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'temporary_login_block_time'], [
                'value' => 600
            ]);
        }

        /* update old order details for addon*/
        $or_details = OrderDetail::with('order')->get();

        foreach ($or_details as $detail){
            $add_on_taxes = [];
            $add_on_prices = [];
            $add_on_tax_amount = 0;

            // Check if add-on ids and quantities are set and non-empty
            if (isset($detail['add_on_ids']) && count(json_decode($detail['add_on_ids'],true)) >0 && isset($detail['add_on_qtys']) && count(json_decode($detail['add_on_qtys'],true)) >0){
                if ($detail->order->order_type == 'pos'){
                    $product_details = json_decode($detail->product_details, true);
                    $add_on_ids = json_decode($detail['add_on_ids'], true);

                    foreach($product_details['add_ons'] as $add_on){
                        if (in_array($add_on['id'], $add_on_ids)) {
                            $add_on_prices[] = $add_on['price'];
                            $add_on_taxes[] = 0;
                        }
                    }
                }else{
                    foreach(json_decode($detail['add_on_ids'], true) as $id){
                        $addon = AddOn::find($id);

                        if ($addon) {
                            $add_on_prices[] = $addon['price'];
                        }else{
                            $add_on_prices[] = 0;
                        }
                        $add_on_taxes[] = 0;
                        $add_on_tax_amount = 0;
                    }
                }
            }else{
                $add_on_taxes = [];
                $add_on_prices = [];
                $add_on_tax_amount = 0;
            }

            // Update the order_details table with the new values
            $detail->add_on_taxes = $add_on_taxes;
            $detail->add_on_prices = $add_on_prices;
            $detail->add_on_tax_amount = $add_on_tax_amount;
            $detail->save();
        }

        if (!BusinessSetting::where(['key' => 'return_page'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'return_page'], [
                'value' => '{"status":"0","content":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'refund_page'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'refund_page'], [
                'value' => '{"status":"0","content":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'cancellation_page'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'cancellation_page'], [
                'value' => '{"status":"0","content":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'offline_payment'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'offline_payment'], [
                'value' => '{"status":"1"}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'guest_checkout'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'guest_checkout'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'partial_payment'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'partial_payment'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'partial_payment_combine_with'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'partial_payment_combine_with'], [
                'value' => 'all'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'qr_code'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'qr_code'], [
                'value' => '{"branch_id":"1","logo":"","title":"","description":"","opening_time":"","closing_time":"","phone":"","website":"","social_media":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'apple_login'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'apple_login'], [
                'value' => '{"login_medium":"apple","client_id":"","client_secret":"","team_id":"","key_id":"","service_file":"","redirect_url":"","status":0}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'add_wallet_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'add_wallet_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'add_wallet_bonus_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'add_wallet_bonus_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }



        if (!Schema::hasTable('payment_requests')) {
            $sql = File::get(base_path($request['path'] . 'database/payment_requests.sql'));
            DB::unprepared($sql);
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'firebase_otp_verification'], [
            'value' => json_encode([
                'status'  => 0,
                'web_api_key' => '',
            ]),
        ]);

        //version_11.0
        $default_preparation_time = Helpers::get_business_settings('default_preparation_time');
        $branches = Branch::where(['preparation_time' => null])->get();
        foreach ($branches as $branch) {
            $branch->preparation_time = $default_preparation_time;
            $branch->save();
        }

        if (!BusinessSetting::where(['key' => 'footer_description_text'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'footer_description_text'], [
                'value' => ''
            ]);
        }

        if (!BusinessSetting::where(['key' => 'push_notification_service_file_content'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'push_notification_service_file_content'], [
                'value' => ''
            ]);
        }

        //version_11.1
        if (!BusinessSetting::where(['key' => 'maintenance_system_setup'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maintenance_system_setup'], [
                'value' => json_encode([])
            ]);
        }
        if (!BusinessSetting::where(['key' => 'maintenance_duration_setup'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maintenance_duration_setup'], [
                'value' => json_encode([
                    'maintenance_duration'  => "until_change",
                    'start_date'  => null,
                    'end_date'  => null,
                ]),
            ]);
        }
        if (!BusinessSetting::where(['key' => 'maintenance_message_setup'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maintenance_message_setup'], [
                'value' => json_encode([
                    'business_number'  => 1,
                    'business_email'  => 1,
                    'maintenance_message'  => "We are Cooking Up Something Special!",
                    'message_body'  => "Our system is currently undergoing maintenance to bring you an even tastier experience. Hang tight while we make the dishes.",
                ]),
            ]);
        }

        $emailVerification = (integer)Helpers::get_business_settings('email_verification') ?? 0;
        $phoneVerification = (integer)Helpers::get_business_settings('phone_verification') ?? 0;

        if (!LoginSetup::where('key', 'email_verification')->exists()) {
            LoginSetup::create([
                'key' => 'email_verification',
                'value' => $emailVerification
            ]);
        }

        if (!LoginSetup::where('key', 'phone_verification')->exists()) {
            LoginSetup::create([
                'key' => 'phone_verification',
                'value' => $phoneVerification
            ]);
        }

        if (!LoginSetup::where('key', 'login_options')->exists())  {
            LoginSetup::create([
                'key' => 'login_options',
                'value' => json_encode([
                    'manual_login' => 1,
                    'otp_login' => 0,
                    'social_media_login' => 0
                ]),
            ]);
        }
        if (!LoginSetup::where('key', 'social_media_for_login')->exists())  {
            LoginSetup::create([
                'key' => 'social_media_for_login',
                'value' => json_encode([
                    'google' => 0,
                    'facebook' => 0,
                    'apple' =>0
                ]),
            ]);
        }

        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && isset($recaptcha['status'], $recaptcha['site_key'], $recaptcha['secret_key']) && $recaptcha['status'] == 1) {
            DB::table('business_settings')->updateOrInsert(['key' => 'recaptcha'], [
                'value' => json_encode([
                    'status' => 0,
                    'site_key' => $recaptcha['site_key'],
                    'secret_key' => $recaptcha['secret_key'],
                ]),
            ]);
        }

        $fixedDeliveryCharge = Helpers::get_business_settings('delivery_charge');
        $branchIds = Branch::pluck('id')->toArray();
        $existingBranchIds = DeliveryChargeSetup::pluck('branch_id')->toArray();

        foreach($branchIds as $branchId) {
            if (!in_array($branchId, $existingBranchIds)) {
                DeliveryChargeSetup::updateOrCreate([
                    'branch_id' => $branchId
                ], [
                    'delivery_charge_type' => 'fixed',
                    'fixed_delivery_charge' => $fixedDeliveryCharge,
                ]);
            }
        }

        if (!BusinessSetting::where(['key' => 'google_map_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'google_map_status'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'admin_order_notification'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'admin_order_notification'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'admin_order_notification_type'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'admin_order_notification_type'], [
                'value' => "manual"
            ]);
        }

        // version 11.3 - System addon removed

        $this->updatePaymobConfigForSupportCountry();
        $this->version_11_4_Update();

        return redirect('/admin/auth/login');
    }



    private function updatePaymobConfigForSupportCountry(): void
    {
        // System addon removed - method no longer needed
    }

    private function version_11_4_Update()
    {
        if (!BusinessSetting::where(['key' => 'customer_referred_discount_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'customer_referred_discount_status'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'customer_referred_discount_type'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'customer_referred_discount_type'], [
                'value' => 'amount'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'customer_referred_discount_amount'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'customer_referred_discount_amount'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'customer_referred_validity_type'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'customer_referred_validity_type'], [
                'value' => 'day'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'customer_referred_validity_value'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'customer_referred_validity_value'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'register_with_referral_code_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'register_with_referral_code_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'referral_code_user_first_order_place_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'referral_code_user_first_order_place_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'referral_code_user_first_order_delivered_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'referral_code_user_first_order_delivered_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }
    }
}
