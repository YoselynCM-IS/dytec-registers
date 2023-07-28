<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Registro;
use App\Student;

class MovimientosExport implements FromView
{
    protected $date;
    protected $status;

    public function __construct($date, $status)
    {
        $this->date = $date;
        $this->status = $status;
    }

    public function view(): View
    {
        $students = $this->get_registers();
        return view('download.movimientos', [
            'students' => $students
        ]);
    }
    
    public function get_registers()
    {
        $registros = Registro::select('student_id')->where('date', 'like', '%'.$this->date.'%')->groupBy('student_id')->get();

        if($this->status == 'accepted'){
            $movimientos = Student::whereIn('id',$registros)
                    ->where('check', $this->status)
                    ->withTrashed()->with('school', 'registros.folio')
                    ->orderBy('created_at', 'desc')->get();
        } else {
            $movimientos = Student::whereIn('id',$registros)
                    ->where('check', $this->status)
                    ->with('school', 'registros.folio')
                    ->orderBy('created_at', 'desc')->get();
        }
        return $movimientos;
    }
}
