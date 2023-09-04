<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use App\Mail\SendRegisters;
use Carbon\Carbon;
use App\Exports\DayExport;
use Excel;

class WorkingHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'working-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo con los estudiantes registrados durante el día hasta las 6:30 PM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $hoy = Carbon::now();
        // $status = ['accepted', 'rejected'];
        
        // $lista = Excel::raw(new DayExport($hoy, $hoy,$status), \Maatwebsite\Excel\Excel::XLSX);
        //     ->send(new SendRegisters($lista, $hoy));
    }
}
