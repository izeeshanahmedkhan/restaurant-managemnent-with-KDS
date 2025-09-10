<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\SocialMedia;
use App\Traits\HelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Support\Renderable;
// Setting model removed
use Illuminate\Support\Facades\Validator;
// Translation model removed
use Illuminate\Validation\ValidationException;


class BusinessSettingsController extends Controller
{
    use HelperTrait;

    public function __construct(
        private BusinessSetting $business_setting,
        private Currency        $currency,
        private SocialMedia     $social_media,
        private Branch          $branch
    )
    {
    }

    /**
     * @return Renderable
     */
    public function restaurantIndex(): Renderable
    {
        if (!$this->business_setting->where(['key' => 'minimum_order_value'])->first()) {
            $this->InsertOrUpdateBusinessData(['key' => 'minimum_order_value'], [
                'value' => 1,
            ]);
        }

        return view('admin-views.business-settings.restaurant-index');
    }

    /**
     * @return JsonResponse
     */
    public function maintenanceMode(): JsonResponse
    {
        $mode = Helpers::get_business_settings('maintenance_mode');
        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_mode'], [
            'value' => isset($mode) ? !$mode : 1
        ]);

        $this->sendMaintenanceModeNotification();
        Cache::forget('maintenance');

        if (!$mode) {
            return response()->json(['message' => translate('Maintenance Mode is On.')]);
        }
        return response()->json(['message' => translate('Maintenance Mode is Off.')]);
    }

    /**
     * @param $side
     * @return JsonResponse
     */
    public function currencySymbolPosition($side): JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'currency_symbol_position'], [
            'value' => $side
        ]);
        return response()->json(['message' => translate('Symbol position is ') . $side]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function restaurantSetup(Request $request): RedirectResponse
    {
        if ($request->has('self_pickup')) {
            $request['self_pickup'] = 1;
        }
        if ($request->has('delivery')) {
            $request['delivery'] = 1;
        }
        // Delivery man functionality removed
        if ($request->has('toggle_veg_non_veg')) {
            $request['toggle_veg_non_veg'] = 1;
        }

        if ($request->has('email_verification')) {
            $request['email_verification'] = 1;
            $request['phone_verification'] = 0;
        } elseif ($request->has('phone_verification')) {
            $request['email_verification'] = 0;
            $request['phone_verification'] = 1;
        }

        $request['guest_checkout'] = $request->has('guest_checkout') ? 1 : 0;
        // Wallet functionality removed
        $request['google_map_status'] = $request->has('google_map_status') ? 1 : 0;
        // Notification functionality removed
        $request['halal_tag_status'] = $request->has('halal_tag_status') ? 1 : 0;

        $this->InsertOrUpdateBusinessData(['key' => 'restaurant_name'], [
            'value' => $request['restaurant_name'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'phone'], [
            'value' => $request['phone'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'country'], [
            'value' => $request['country']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'time_zone'], [
            'value' => $request['time_zone'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'phone_verification'], [
            'value' => $request['phone_verification']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'email_verification'], [
            'value' => $request['email_verification']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'self_pickup'], [
            'value' => $request['self_pickup'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'delivery'], [
            'value' => $request['delivery'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'restaurant_open_time'], [
            'value' => $request['restaurant_open_time'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'restaurant_close_time'], [
            'value' => $request['restaurant_close_time'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'currency'], [
            'value' => $request['currency'],
        ]);

        $currentLogo = $this->business_setting->where(['key' => 'logo'])->first();
        if ($request->hasFile('logo')) {
            $logoValue = Helpers::update('restaurant/', $currentLogo ? $currentLogo->value : '', 'png', $request->file('logo'));
        $this->InsertOrUpdateBusinessData(['key' => 'logo'], [
                'value' => $logoValue
        ]);
        } elseif ($currentLogo) {
            $this->InsertOrUpdateBusinessData(['key' => 'logo'], [
                'value' => $currentLogo->value
            ]);
        }

        $this->InsertOrUpdateBusinessData(['key' => 'phone'], [
            'value' => $request['phone'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'email_address'], [
            'value' => $request['email'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'address'], [
            'value' => $request['address'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'email_verification'], [
            'value' => $request['email_verification'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'footer_text'], [
            'value' => $request['footer_text'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'point_per_currency'], [
            'value' => $request['point_per_currency'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'pagination_limit'], [
            'value' => $request['pagination_limit'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'decimal_point_settings'], [
            'value' => $request['decimal_point_settings']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'time_format'], [
            'value' => $request['time_format']
        ]);

        $currentFavIcon = $this->business_setting->where(['key' => 'fav_icon'])->first();
        if ($request->hasFile('fav_icon')) {
            $faviconValue = Helpers::update('restaurant/', $currentFavIcon ? $currentFavIcon->value : '', 'png', $request->file('fav_icon'));
        $this->InsertOrUpdateBusinessData(['key' => 'fav_icon'], [
                'value' => $faviconValue
            ]);
        } elseif ($currentFavIcon) {
            $this->InsertOrUpdateBusinessData(['key' => 'fav_icon'], [
                'value' => $currentFavIcon->value
            ]);
        }

        // Delivery man functionality removed

        $this->InsertOrUpdateBusinessData(['key' => 'toggle_veg_non_veg'], [
            'value' => $request['toggle_veg_non_veg'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'guest_checkout'], [
            'value' => $request['guest_checkout'],
        ]);

        // Wallet functionality removed

        $this->InsertOrUpdateBusinessData(['key' => 'footer_description_text'], [
            'value' => $request['footer_description_text'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'google_map_status'], [
            'value' => $request['google_map_status'],
        ]);

        // Notification functionality removed

        $this->InsertOrUpdateBusinessData(['key' => 'halal_tag_status'], [
            'value' => $request['halal_tag_status'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    // Mail functionality removed

    /**
     * @return Renderable
     */
    // Payment index functionality removed

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    // Payment method status functionality removed

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    // Payment config update functionality removed

    /**
     * @return Renderable
     */
    public function termsAndConditions(): Renderable
    {
        $tnc = $this->business_setting->where(['key' => 'terms_and_conditions'])->first();
        if (!$tnc) {
            $this->business_setting->insert([
                'key' => 'terms_and_conditions',
                'value' => '',
            ]);
        }
        return view('admin-views.business-settings.terms-and-conditions', compact('tnc'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function termsAndConditionsUpdate(Request $request): RedirectResponse
    {
        $this->business_setting->where(['key' => 'terms_and_conditions'])->update([
            'value' => $request->tnc,
        ]);

        Toastr::success(translate('Terms and Conditions updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function privacyPolicy(): Renderable
    {
        $data = $this->business_setting->where(['key' => 'privacy_policy'])->first();
        if (!$data) {
            $data = [
                'key' => 'privacy_policy',
                'value' => '',
            ];
            $this->business_setting->insert($data);
        }

        return view('admin-views.business-settings.privacy-policy', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function privacyPolicyUpdate(Request $request): RedirectResponse
    {
        $this->business_setting->where(['key' => 'privacy_policy'])->update([
            'value' => $request->privacy_policy,
        ]);

        Toastr::success(translate('Privacy policy updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function aboutUs(): Renderable
    {
        $data = $this->business_setting->where(['key' => 'about_us'])->first();
        if (!$data) {
            $data = [
                'key' => 'about_us',
                'value' => '',
            ];
            $this->business_setting->insert($data);
        }

        return view('admin-views.business-settings.about-us', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function aboutUsUpdate(Request $request): RedirectResponse
    {
        $this->business_setting->where(['key' => 'about_us'])->update([
            'value' => $request->about_us,
        ]);

        Toastr::success(translate('About us updated!'));
        return back();
    }


    /**
     * @param Request $request
     * @return Renderable
     */
    public function returnPageIndex(Request $request): Renderable
    {
        $data = $this->business_setting->where(['key' => 'return_page'])->first();

        if (!$data) {
            $data = [
                'key' => 'return_page',
                'value' => json_encode([
                    'status' => 0,
                    'content' => ''
                ]),
            ];
            $this->business_setting->insert($data);
        }

        return view('admin-views.business-settings.return_page-index', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function returnPageUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'return_page'], [
            'value' => json_encode([
                'status' => $request['status'] == 1 ? 1 : 0,
                'content' => $request['content']
            ]),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }


    /**
     * @return Renderable
     */
    public function refundPageIndex(): Renderable
    {
        $data = $this->business_setting->where(['key' => 'refund_page'])->first();

        if (!$data) {
            $data = [
                'key' => 'refund_page',
                'value' => json_encode([
                    'status' => 0,
                    'content' => ''
                ]),
            ];
            $this->business_setting->insert($data);
        }

        return view('admin-views.business-settings.refund_page-index', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function refundPageUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'refund_page'], [
            'value' => json_encode([
                'status' => $request['status'] == 1 ? 1 : 0,
                'content' => $request['content'] == null ? null : $request['content']
            ]),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }


    /**
     * @return Renderable
     */
    public function cancellationPageIndex(): Renderable
    {
        $data = $this->business_setting->where(['key' => 'cancellation_page'])->first();

        if (!$data) {
            $data = [
                'key' => 'cancellation_page',
                'value' => json_encode([
                    'status' => 0,
                    'content' => ''
                ]),
            ];
            $this->business_setting->insert($data);
        }

        return view('admin-views.business-settings.cancellation_page-index', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancellationPageUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'cancellation_page'], [
            'value' => json_encode([
                'status' => $request['status'] == 1 ? 1 : 0,
                'content' => $request['content']
            ]),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function fcmIndex(): Renderable
    {
        $data = $this->business_setting->where(['key' => 'order_pending_message'])->first();
        if (!$this->business_setting->where(['key' => 'order_pending_message'])->first()) {
            $this->business_setting->insert([
                'key' => 'order_pending_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'order_confirmation_msg'])->first()) {
            $this->business_setting->insert([
                'key' => 'order_confirmation_msg',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'order_processing_message'])->first()) {
            $this->business_setting->insert([
                'key' => 'order_processing_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'out_for_delivery_message'])->first()) {
            $this->business_setting->insert([
                'key' => 'out_for_delivery_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'order_delivered_message'])->first()) {
            $this->business_setting->insert([
                'key' => 'order_delivered_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'delivery_boy_assign_message'])->first()) {
            $this->business_setting->insert([
                'key' => 'delivery_boy_assign_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'delivery_boy_start_message'])->first()) {
            $this->business_setting->insert([
                'key' => 'delivery_boy_start_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'delivery_boy_delivered_message'])->first()) {
            $this->business_setting->insert([
                'key' => 'delivery_boy_delivered_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'customer_notify_message'])->first()) {
            $this->business_setting->insert([
                'key' => 'customer_notify_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'customer_notify_message_for_time_change'])->first()) {
            $this->business_setting->insert([
                'key' => 'customer_notify_message_for_time_change',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        return view('admin-views.business-settings.fcm-index');
    }

    /**
     * @return Application|Factory|View
     */
    public function fcmConfig(): Factory|View|Application
    {
        if (!$this->business_setting->where(['key' => 'fcm_topic'])->first()) {
            $this->business_setting->insert([
                'key' => 'fcm_topic',
                'value' => '',
            ]);
        }
        if (!$this->business_setting->where(['key' => 'fcm_project_id'])->first()) {
            $this->business_setting->insert([
                'key' => 'fcm_project_id',
                'value' => '',
            ]);
        }
        if (!$this->business_setting->where(['key' => 'push_notification_key'])->first()) {
            $this->business_setting->insert([
                'key' => 'push_notification_key',
                'value' => '',
            ]);
        }

        $fcm_credentials = Helpers::get_business_settings('fcm_credentials');

        return view('admin-views.business-settings.fcm-config', compact('fcm_credentials'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcm(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'push_notification_service_file_content'], [
            'value' => $request['push_notification_service_file_content'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'fcm_project_id'], [
            'value' => $request['projectId'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'fcm_credentials'], [
            'value' => json_encode([
                'apiKey' => $request->apiKey,
                'authDomain' => $request->authDomain,
                'projectId' => $request->projectId,
                'storageBucket' => $request->storageBucket,
                'messagingSenderId' => $request->messagingSenderId,
                'appId' => $request->appId,
                'measurementId' => $request->measurementId
            ])
        ]);


        self::firebase_message_config_file_gen();

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return void
     */
    function firebase_message_config_file_gen(): void
    {
        $config = Helpers::get_business_settings('fcm_credentials');

        $apiKey = $config['apiKey'] ?? '';
        $authDomain = $config['authDomain'] ?? '';
        $projectId = $config['projectId'] ?? '';
        $storageBucket = $config['storageBucket'] ?? '';
        $messagingSenderId = $config['messagingSenderId'] ?? '';
        $appId = $config['appId'] ?? '';
        $measurementId = $config['measurementId'] ?? '';

        $filePath = base_path('firebase-messaging-sw.js');

        try {
            if (file_exists($filePath) && !is_writable($filePath)) {
                if (!chmod($filePath, 0644)) {
                    throw new \Exception('File is not writable and permission change failed: ' . $filePath);
                }
            }

            $fileContent = <<<JS
                importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
                importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

                firebase.initializeApp({
                    apiKey: "$apiKey",
                    authDomain: "$authDomain",
                    projectId: "$projectId",
                    storageBucket: "$storageBucket",
                    messagingSenderId: "$messagingSenderId",
                    appId: "$appId",
                    measurementId: "$measurementId"
                });

                const messaging = firebase.messaging();
                messaging.setBackgroundMessageHandler(function (payload) {
                    return self.registration.showNotification(payload.data.title, {
                        body: payload.data.body ? payload.data.body : '',
                        icon: payload.data.icon ? payload.data.icon : ''
                    });
                });
                JS;


            if (file_put_contents($filePath, $fileContent) === false) {
                throw new \Exception('Failed to write to file: ' . $filePath);
            }

        } catch (\Exception $e) {
            //
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcmMessages(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'order_pending_message'], [
            'value' => json_encode([
                'status' => $request['pending_status'] == 1 ? 1 : 0,
                'message' => $request['pending_message'],
            ]),
        ]);
        $pendingOrder = $this->business_setting->where('key', 'order_pending_message')->first();

        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->order_pending_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'order_confirmation_msg'], [
            'value' => json_encode([
                'status' => $request['confirm_status'] == 1 ? 1 : 0,
                'message' => $request['confirm_message'],
            ]),
        ]);
        $confirmOrder = $this->business_setting->where('key', 'order_confirmation_msg')->first();

        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->order_confirmation_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'order_processing_message'], [
            'value' => json_encode([
                'status' => $request['processing_status'] == 1 ? 1 : 0,
                'message' => $request['processing_message'],
            ]),
        ]);
        $processingOrder = $this->business_setting->where('key', 'order_processing_message')->first();

        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->order_processing_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }


        $this->InsertOrUpdateBusinessData(['key' => 'out_for_delivery_message'], [
            'value' => json_encode([
                'status' => $request['out_for_delivery_status'] == 1 ? 1 : 0,
                'message' => $request['out_for_delivery_message'],
            ]),
        ]);
        $outForDelivery = $this->business_setting->where('key', 'out_for_delivery_message')->first();

        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->order_out_for_delivery_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'order_delivered_message'], [
            'value' => json_encode([
                'status' => $request['delivered_status'] == 1 ? 1 : 0,
                'message' => $request['delivered_message'],
            ]),
        ]);
        $orderDelivered = $this->business_setting->where('key', 'order_delivered_message')->first();

        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->order_delivered_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'delivery_boy_assign_message'], [
            'value' => json_encode([
                'status' => $request['delivery_boy_assign_status'] == 1 ? 1 : 0,
                'message' => $request['delivery_boy_assign_message'],
            ]),
        ]);
        // Delivery man functionality removed

        $this->InsertOrUpdateBusinessData(['key' => 'delivery_boy_start_message'], [
            'value' => json_encode([
                'status' => $request['delivery_boy_start_status'] == 1 ? 1 : 0,
                'message' => $request['delivery_boy_start_message'],
            ]),
        ]);
        // Delivery man functionality removed

        $this->InsertOrUpdateBusinessData(['key' => 'delivery_boy_delivered_message'], [
            'value' => json_encode([
                'status' => $request['delivery_boy_delivered_status'] == 1 ? 1 : 0,
                'message' => $request['delivery_boy_delivered_message'],
            ]),
        ]);
        // Delivery man functionality removed

        $this->InsertOrUpdateBusinessData(['key' => 'customer_notify_message'], [
            'value' => json_encode([
                'status' => $request['customer_notify_status'] == 1 ? 1 : 0,
                'message' => $request['customer_notify_message'],
            ]),
        ]);
        $customerNotify = $this->business_setting->where('key', 'customer_notify_message')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->customer_notification_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'customer_notify_message_for_time_change'], [
            'value' => json_encode([
                'status' => $request['customer_notify_status_for_time_change'] == 1 ? 1 : 0,
                'message' => $request['customer_notify_message_for_time_change'],
            ]),
        ]);
        $notifyForTimeChange = $this->business_setting->where('key', 'customer_notify_message_for_time_change')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->notify_for_time_change_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'returned_message'], [
            'value' => json_encode([
                'status' => $request['returned_status'] == 1 ? 1 : 0,
                'message' => $request['returned_message'],
            ]),
        ]);
        $returnOrder = $this->business_setting->where('key', 'returned_message')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->return_order_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'failed_message'], [
            'value' => json_encode([
                'status' => $request['failed_status'] == 1 ? 1 : 0,
                'message' => $request['failed_message'],
            ]),
        ]);
        $failedOrder = $this->business_setting->where('key', 'failed_message')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->failed_order_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'canceled_message'], [
            'value' => json_encode([
                'status' => $request['canceled_status'] == 1 ? 1 : 0,
                'message' => $request['canceled_message'],
            ]),
        ]);
        $canceledOrder = $this->business_setting->where('key', 'canceled_message')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->canceled_order_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'add_wallet_message'], [
            'value' => json_encode([
                'status' => $request['add_wallet_status'] == 1 ? 1 : 0,
                'message' => $request['add_wallet_message'],
            ]),
        ]);
        $addWallet = $this->business_setting->where('key', 'add_wallet_message')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->add_fund_wallet_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        // Wallet functionality removed

        $this->InsertOrUpdateBusinessData(['key' => 'register_with_referral_code_message'], [
            'value' => json_encode([
                'status' => $request['register_with_referral_code_status'] == 1 ? 1 : 0,
                'message' => $request['register_with_referral_code_message'],
            ]),
        ]);

        $register_with_referral_code = $this->business_setting->where('key', 'register_with_referral_code_message')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->register_with_referral_code_bonus_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'referral_code_user_first_order_place_message'], [
            'value' => json_encode([
                'status' => $request['referral_code_user_first_order_place_status'] == 1 ? 1 : 0,
                'message' => $request['referral_code_user_first_order_place_message'],
            ]),
        ]);

        $register_with_referral_code = $this->business_setting->where('key', 'referral_code_user_first_order_place_message')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->referral_code_user_first_order_place_bonus_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'referral_code_user_first_order_delivered_message'], [
            'value' => json_encode([
                'status' => $request['referral_code_user_first_order_delivered_status'] == 1 ? 1 : 0,
                'message' => $request['referral_code_user_first_order_delivered_message'],
            ]),
        ]);

        $register_with_referral_code = $this->business_setting->where('key', 'referral_code_user_first_order_delivered_message')->first();
        foreach ($request->lang as $index => $key) {
            if ($key === 'default') {
                continue;
            }
            $message = $request->referral_code_user_first_order_delivered_bonus_message[$index - 1] ?? null;
            if ($message !== null) {
                // Translation functionality removed - always use English
            }
        }

        Toastr::success(translate('Message updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    // Map API functionality removed

    /**
     * @param Request $request
     * @return Renderable
     */
    public function recaptchaIndex(Request $request): Renderable
    {
        return view('admin-views.business-settings.recaptcha-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function recaptchaUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'recaptcha'], [
            'value' => json_encode([
                'status' => $request['status'],
                'site_key' => $request['site_key'],
                'secret_key' => $request['secret_key']
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function appSettingIndex(): Renderable
    {
        return view('admin-views.business-settings.app-setting-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function appSettingUpdate(Request $request): RedirectResponse
    {
        if ($request->platform == 'android') {
            $this->InsertOrUpdateBusinessData(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $request['play_store_status'],
                    'link' => $request['play_store_link'],
                    'min_version' => $request['android_min_version'],

                ]),
            ]);

            Toastr::success(translate('Updated Successfully for Android'));
            return back();
        }

        if ($request->platform == 'ios') {
            $this->InsertOrUpdateBusinessData(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $request['app_store_status'],
                    'link' => $request['app_store_link'],
                    'min_version' => $request['ios_min_version'],
                ]),
            ]);
            Toastr::success(translate('Updated Successfully for IOS'));
            return back();
        }

        Toastr::error(translate('Updated failed'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function firebaseMessageConfigIndex(): Renderable
    {
        return view('admin-views.business-settings.firebase-config-index');
    }

    /**
     * @return Renderable
     */
    // Social media functionality removed

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateDeliveryFee(Request $request): RedirectResponse
    {
        $deliveryCharge = $request->delivery_charge;
        if ($deliveryCharge == null) {
            $deliveryCharge = $this->business_setting->where(['key' => 'delivery_charge'])->first()->value;
        }
        $this->InsertOrUpdateBusinessData(['key' => 'delivery_charge'], [
            'value' => $deliveryCharge,
        ]);

        // Delivery man functionality removed - using default values
        if ($request['min_shipping_charge'] == null) {
            $request['min_shipping_charge'] = 0;
        }
        if ($request['shipping_per_km'] == null) {
            $request['shipping_per_km'] = 0;
        }
        if ($request['shipping_status'] == 1) {
            $request->validate([
                'min_shipping_charge' => 'required',
                'shipping_per_km' => 'required',
            ],
                [
                    'min_shipping_charge.required' => 'Minimum shipping charge is required while shipping method is active',
                    'shipping_per_km.required' => 'Shipping charge per Kilometer is required while shipping method is active',
                ]);
        }

        $this->InsertOrUpdateBusinessData(['key' => 'delivery_management'], [
            'value' => json_encode([
                'status' => $request['shipping_status'],
                'min_shipping_charge' => $request['min_shipping_charge'],
                'shipping_per_km' => $request['shipping_per_km'],
            ]),
        ]);

        Toastr::success(translate('Delivery_fee_updated_successfully'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function mainBranchSetup(): Renderable
    {
        $branch = $this->branch->find(1);
        return view('admin-views.business-settings.restaurant.main-branch', compact('branch'));
    }

    /**
     * @return Renderable
     */
    public function socialLogin(): Renderable
    {
        $apple = BusinessSetting::where('key', 'apple_login')->first();
        if (!$apple) {
            $this->InsertOrUpdateBusinessData(['key' => 'apple_login'], [
                'value' => '{"login_medium":"apple","client_id":"","client_secret":"","team_id":"","key_id":"","service_file":"","redirect_url":"","status":""}',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $apple = BusinessSetting::where('key', 'apple_login')->first();
        }
        $appleLoginService = json_decode($apple->value, true);
        return view('admin-views.business-settings.social-login', compact('appleLoginService'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateAppleLogin(Request $request): RedirectResponse
    {
        $apple_login = Helpers::get_business_settings('apple_login');

        if ($request->hasFile('service_file')) {
            $fileName = Helpers::upload('apple-login/', 'p8', $request->file('service_file'));
        }

        $data = [
            'value' => json_encode([
                'login_medium' => 'apple',
                'client_id' => $request['client_id'],
                'client_secret' => '',
                'team_id' => $request['team_id'],
                'key_id' => $request['key_id'],
                'service_file' => $fileName ?? $apple_login['service_file'],
                'redirect_url' => '',
            ]),
        ];

        $this->InsertOrUpdateBusinessData(['key' => 'apple_login'], $data);

        Toastr::success(translate('settings updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function chatIndex(): Renderable
    {
        return view('admin-views.business-settings.chat-index');
    }

    /**
     * @param Request $request
     * @param $name
     * @return RedirectResponse
     */
    public function chatUpdate(Request $request, $name): RedirectResponse
    {
        if ($name == 'whatsapp') {
            $this->InsertOrUpdateBusinessData(['key' => 'whatsapp'], [
                'value' => json_encode([
                    'status' => $request['status'] == 'on' ? 1 : 0,
                    'number' => $request['number'],
                ]),
            ]);
        }

        Toastr::success(translate('chat settings updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function cookiesSetup(): Renderable
    {
        return view('admin-views.business-settings.cookies-setup-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cookiesSetupUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'cookies'], [
            'value' => json_encode([
                'status' => $request['status'],
                'text' => $request['text'],
            ])
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function OTPSetup(): Factory|View|Application
    {
        return view('admin-views.business-settings.otp-setup');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function OTPSetupUpdate(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'maximum_otp_hit'], [
            'value' => $request['maximum_otp_hit'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'otp_resend_time'], [
            'value' => $request['otp_resend_time'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'temporary_block_time'], [
            'value' => $request['temporary_block_time'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'maximum_login_hit'], [
            'value' => $request['maximum_login_hit'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'temporary_login_block_time'], [
            'value' => $request['temporary_login_block_time'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function orderIndex(): Factory|View|Application
    {
        return view('admin-views.business-settings.order-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function orderUpdate(Request $request): RedirectResponse
    {
        $request['cutlery_status'] = $request->has('cutlery_status') ? 1 : 0;

        $this->InsertOrUpdateBusinessData(['key' => 'minimum_order_value'], [
            'value' => $request['minimum_order_value'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'default_preparation_time'], [
            'value' => $request['default_preparation_time'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'schedule_order_slot_duration'], [
            'value' => $request['schedule_order_slot_duration']
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'cutlery_status'], [
            'value' => $request['cutlery_status']
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function customerSettings(): Factory|View|Application
    {
        // Wallet and loyalty functionality removed
        $data = [];
        return view('admin-views.business-settings.customer-settings', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function customerSettingsUpdate(Request $request): RedirectResponse
    {
        // Wallet, loyalty, and referral functionality removed
        // No validation or data processing needed

        Toastr::success(translate('customer_settings_updated_successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    // Firebase OTP verification functionality removed

    public function productIndex(): Factory|View|Application
    {
        $searchPlaceholder = $this->business_setting->where('key', 'search_placeholder')->first();
        return view('admin-views.business-settings.product-index', compact('searchPlaceholder'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function searchPlaceholderStore(Request $request): RedirectResponse
    {
        $request->validate([
            'placeholder_name' => 'required',
        ]);

        $searchPlaceholder = $this->business_setting->where('key', 'search_placeholder')->first();
        if ($searchPlaceholder) {
            $data = json_decode($searchPlaceholder->value, true);
        } else {
            $data = [];
        }

        $id = $request->input('id');
        $existingSearchPlaceholder = null;
        foreach ($data as $key => $item) {
            if ($item['id'] == (int)$id) {
                $existingSearchPlaceholder = $key;
                break;
            }
        }

        if ($existingSearchPlaceholder !== null) {
            $data[$existingSearchPlaceholder]['id'] = (int)$id;
            $data[$existingSearchPlaceholder]['placeholder_name'] = $request['placeholder_name'];
        } else {
            $newItem = [
                'id' => rand(1000000000, 9999999999),
                'placeholder_name' => $request['placeholder_name'],
                'status' => "1",
            ];

            $data[] = $newItem;
        }

        $this->business_setting->query()->updateOrInsert(['key' => 'search_placeholder'], [
            'value' => json_encode($data)
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function searchPlaceholderStatus($id): RedirectResponse
    {
        $searchPlaceholder = $this->business_setting->where('key', 'search_placeholder')->first();
        if ($searchPlaceholder) {
            $data = json_decode($searchPlaceholder->value, true);
        } else {
            $data = [];
        }
        foreach ($data as $value) {
            if ($value['id'] == $id) {
                $value['status'] = ($value['status'] == 0) ? 1 : 0;
            }
            $array[] = $value;
        }

        $this->business_setting->query()->updateOrInsert(['key' => 'search_placeholder'], [
            'value' => $array
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function searchPlaceholderDelete($id): RedirectResponse
    {
        $searchPlaceholder = $this->business_setting->where('key', 'search_placeholder')->first();
        if ($searchPlaceholder) {
            $data = json_decode($searchPlaceholder->value, true);
        } else {
            $data = [];
        }
        foreach ($data as $value) {
            if ($value['id'] != $id) {
                $array[] = $value;
            }
        }
        $this->business_setting->query()->updateOrInsert(['key' => 'search_placeholder'], [
            'value' => $array
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function maintenanceModeSetup(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_mode'], [
            'value' => $request->has('maintenance_mode') ? 1 : 0
        ]);

        $selectedSystems = [];
        $systems = ['branch_panel', 'customer_app', 'web_app', 'table_app', 'kitchen_app'];

        foreach ($systems as $system) {
            if ($request->has($system)) {
                $selectedSystems[] = $system;
            }
        }

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_system_setup'], [
            'value' => json_encode($selectedSystems)],
        );

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_duration_setup'], [
            'value' => json_encode([
                'maintenance_duration' => $request['maintenance_duration'],
                'start_date' => $request['start_date'] ?? null,
                'end_date' => $request['end_date'] ?? null,
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_message_setup'], [
            'value' => json_encode([
                'business_number' => $request->has('business_number') ? 1 : 0,
                'business_email' => $request->has('business_email') ? 1 : 0,
                'maintenance_message' => $request['maintenance_message'],
                'message_body' => $request['message_body']
            ]),
        ]);

        $maintenanceStatus = (integer)(Helpers::get_business_settings('maintenance_mode') ?? 0);
        $selectedMaintenanceDuration = Helpers::get_business_settings('maintenance_duration_setup') ?? [];
        $selectedMaintenanceSystem = Helpers::get_business_settings('maintenance_system_setup') ?? [];
        $isBranch = in_array('branch_panel', $selectedMaintenanceSystem) ? 1 : 0;

        $maintenance = [
            'status' => $maintenanceStatus,
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
            'branch_panel' => $isBranch,
            'maintenance_duration' => $selectedMaintenanceDuration['maintenance_duration'],
            'maintenance_messages' => Helpers::get_business_settings('maintenance_message_setup') ?? [],
        ];


        Cache::put('maintenance', $maintenance, now()->addYears(1));

        $this->sendMaintenanceModeNotification();

        Toastr::success(translate('Settings updated!'));
        return back();

    }

    private function sendMaintenanceModeNotification(): void
    {
        $data = [
            'title' => translate('Maintenance Mode Settings Updated'),
            'description' => translate('Maintenance Mode Settings Updated'),
            'order_id' => '',
            'image' => ''
        ];

        try {
            Helpers::send_push_notif_to_topic($data, 'notify', 'maintenance');
            // Delivery man functionality removed
        } catch (\Exception $e) {
        }
    }

    // Marketing tools functionality removed

    /**
     * @param $key
     * @param $value
     * @return void
     */
    private function InsertOrUpdateBusinessData($key, $value): void
    {
        $businessSetting = $this->business_setting->where(['key' => $key['key']])->first();
        if ($businessSetting) {
            $businessSetting->value = $value['value'];
            $businessSetting->save();
        } else {
            $this->business_setting->create([
                'key' => $key['key'],
                'value' => $value['value'],
            ]);
        }
    }

}
