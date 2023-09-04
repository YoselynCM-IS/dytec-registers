<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreRegister extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $message;
    private $student;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $student)
    {
        $this->message = $message;
        $this->student = $student;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // PENDIENTE EL CORREO DE ENVIO
        return $this->from('registro.pago@digitaltecenglishcenter.com')
            ->cc("dytec.pagos@gmail.com")
            ->subject(__("Registro de comprobante de pago"))
            ->markdown('mails.save-pre-register') //Template
            ->with('student', $this->student)
            ->with('message', $this->message);
    }
}
