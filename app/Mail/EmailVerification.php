<?php

namespace App\Mail;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $token;

    public function __construct($token = '', $language_code)
    {
        $this->token = $token;
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
        $company_name = BusinessSetting::where('key', 'restaurant_name')->first()->value;
        
        return $this->subject(translate('Email_Verification'))
            ->view('emails.email-verification', [
                'company_name' => $company_name,
                'code' => $code
            ]);
    }
}
