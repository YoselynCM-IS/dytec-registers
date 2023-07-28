<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class DayExport implements WithMultipleSheets
{
    
    use Exportable;

    protected $fecha1;
    protected $fecha2;
    protected $status;
    
    public function __construct($fecha1, $fecha2, $status)
    {
        $this->fecha1 = $fecha1;
        $this->fecha2 = $fecha2;
        $this->status = $status;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        foreach ($this->status as $state) {
            $sheets[] = new DayStatusExport($state, $this->fecha1, $this->fecha2);
        }

        return $sheets;
    }
}
