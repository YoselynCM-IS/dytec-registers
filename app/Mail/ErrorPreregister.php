<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorPreregister extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $error;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($error)
    {
        $this->error = $error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('registro.pago@digitaltecenglishcenter.com')
        ->cc("dytec.pagos@gmail.com")
        ->subject(__("Status Preregister"))
        ->markdown('mails.error-preresgister') //Template
        ->with('error', $this->error);
    }
}
