<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $download_url;

    public function __construct($download_url)
    {
        $this->download_url = $download_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $download_url = $this->download_url;
        return $this->subject(translate('Download_Invoice'))->view('email-templates.customer-invoice', ['url' => $download_url]);
    }
}
