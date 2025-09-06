<?php

namespace App\Mail;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DMSelfRegistration extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $status;
    protected $name;

    public function __construct($status, $name, $language_code)
    {
        $this->status = $status;
        $this->name = $name;
        $this->language_code = $language_code;
    }

    /**
     * @return DMSelfRegistration
     */
    public function build()
    {
        $status = $this->status;
        $dm_name = $this->name;
        $company_name = BusinessSetting::where('key', 'restaurant_name')->first()->value;
        
        return $this->subject(translate('Deliveryman_Registration_Mail'))
            ->view('emails.dm-registration', [
                'company_name' => $company_name,
                'dm_name' => $dm_name,
                'status' => $status
            ]);
    }
}
