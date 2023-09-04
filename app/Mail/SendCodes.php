<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCodes extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $name;
    private $code;
    private $code2;
    private $code3;
    private $book;
    private $editorial;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $code, $code2, $code3, $book, $editorial)
    {
        $this->name = $name;
        $this->code = $code;
        $this->code2 = $code2;
        $this->code3 = $code3;
        $this->book = $book;
        $this->editorial = $editorial;
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
            ->subject(__("Código"))
            ->markdown('mails.send-code')
            ->with('name', $this->name)
            ->with('code', $this->code)
            ->with('code2', $this->code2)
            ->with('code3', $this->code3)
            ->with('book', $this->book)
            ->with('editorial', $this->editorial);
    }
}
