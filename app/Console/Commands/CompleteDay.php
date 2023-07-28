<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use App\Mail\SendRegisters;
use Carbon\Carbon;
use App\Exports\DayExport;
use Excel;

class CompleteDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'complet-day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo con los estudiantes registrados durante el día anterior';

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
        $yesterday = new Carbon('yesterday');
        $status = ['accepted', 'rejected'];

        if($yesterday->format('l') == 'Friday'){
            $status = ['all_week', 'accepted', 'rejected'];
        }
        $lista = Excel::raw(new DayExport($yesterday, $yesterday, $status), \Maatwebsite\Excel\Excel::XLSX);
        
        // Mail::to('yosecmart@gmail.com')
        //     ->cc(['yoscm2@gmail.com'])
        Mail::to('alma.omega09@gmail.com')
            ->cc(['jennyomega7@gmail.com'])
            ->send(new SendRegisters($lista, $yesterday));
    }
}
