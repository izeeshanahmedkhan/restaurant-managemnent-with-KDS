<?php

namespace App\Mail;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $token;

    public function __construct($token, $name, $language_code)
    {
        $this->token = $token;
        $this->name = $name;
        $this->language_code = $language_code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $code = $this->token;
        $customer_name = $this->name;
        $company_name = BusinessSetting::where('key', 'restaurant_name')->first()->value;
        
        return $this->subject(translate('Customer_Password_Reset_mail'))
            ->view('emails.password-reset', [
                'company_name' => $company_name,
                'customer_name' => $customer_name,
                'code' => $code
            ]);
    }
}
