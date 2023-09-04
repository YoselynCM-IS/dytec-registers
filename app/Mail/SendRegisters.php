<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use App\Exports\DayExport;
use Excel;

class SendRegisters extends Mailable
{
    use Queueable, SerializesModels;

    private $lista;
    private $day;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($lista, $day)
    {
        $this->lista = $lista;
        $this->day = $day;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fecha = $this->day->format('d-m-Y h:i');
        $nombre = $fecha.'-registros'.'.xlsx';

        return $this->from('registro.pago@digitaltecenglishcenter.com')
            ->cc("dytec.pagos@gmail.com")
            ->subject(__($fecha))
            ->attachData($this->lista, $nombre)
            ->markdown('mails.send-registers')
            ->with('fecha', $fecha);
    }
}
