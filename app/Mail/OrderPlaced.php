<?php

namespace App\Mail;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use App\Model\CustomerAddress;
use App\Model\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;


class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $order_id;

    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $order_id = $this->order_id;
        $order=Order::where('id', $order_id)->first();

        $address = $order->delivery_address ?? CustomerAddress::find($order->delivery_address_id);
        $order->address = $address;

        $company_name = BusinessSetting::where('key', 'restaurant_name')->first()->value;
        $user_name = $order?->customer?->f_name.' '.$order?->customer?->l_name;
        $restaurant_name = $order?->branch->name;
        $delivery_man_name = null; // Delivery man functionality removed

        // Generate Invoice PDF
        $view = View::make('emails.invoice', compact('order'))->render();

        $mpdf = new Mpdf([
            'tempDir' => storage_path('tmp'),
            'default_font' => 'dejavusans',
            'mode' => 'utf-8',
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf->WriteHTML($view);
        $pdfContent = $mpdf->Output('', 'S');

        return $this->subject(translate('Order_Place_Mail'))
            ->view('emails.order-placed', [
                'company_name' => $company_name,
                'user_name' => $user_name,
                'restaurant_name' => $restaurant_name,
                'delivery_man_name' => $delivery_man_name,
                'order' => $order
            ])
            ->attachData($pdfContent, 'Invoice_Order_' . $order->id . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
