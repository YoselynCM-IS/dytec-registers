<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class DayStatusExport implements FromView, WithTitle
{
    protected $state;
    protected $fecha1;
    protected $fecha2;
    
    public function __construct($state, $fecha1, $fecha2)
    {
        $this->state = $state;
        $this->fecha1 = $fecha1;
        $this->fecha2 = $fecha2;
    }

    public function view(): View
    {
        $students = $this->get_registers();
        return view('download.registros-day', [
            'students' => $students
        ]);
    }

    public function get_registers()
    {
        $fecha1 = $this->fecha1->format('Y-m-d');
        $fecha2 = $this->fecha2->format('Y-m-d');

        if($this->state == 'accepted'){
            $registros = \DB::table('registros')
                    ->select('students.created_at as fecha_registro', 'schools.name as school', 'students.name as name', 'students.book as book',
                        'students.numcuenta as numcuenta', 'registros.date as date', 'registros.type as type', 'registros.bank as bank', 'registros.invoice as invoice', 
                        'registros.auto as auto', 'registros.cajero as cajero', 'registros.total as total', 'folios.fecha as fecha', 'folios.concepto as concepto', 'folios.abono as abono')
                    ->join('students', 'registros.student_id', '=', 'students.id')
                    ->join('folios', 'registros.folio_id', '=', 'folios.id')
                    ->join('schools', 'students.school_id', '=', 'schools.id')
                    ->whereBetween('students.created_at',[$fecha1, $fecha2])
                    ->where('students.check', 'accepted')
                    ->where(function($query) {
                        $query->where('students.deleted_at', '!=', NULL )
                                ->orWhere('students.deleted_at', '=', NULL);
                    })
                    ->orderBy('registros.total', 'asc')->get();
        } 
        if($this->state == 'all_week'){
            $saturday = new Carbon('last saturday');
            $registros = \DB::table('registros')
                    ->select('students.created_at as fecha_registro', 'schools.name as school', 'students.name as name', 'students.book as book',
                        'students.numcuenta as numcuenta', 'registros.date as date', 'registros.type as type', 'registros.bank as bank', 'registros.invoice as invoice', 
                        'registros.auto as auto', 'registros.cajero as cajero', 'registros.total as total', 'folios.fecha as fecha', 'folios.concepto as concepto', 'folios.abono as abono')
                    ->join('students', 'registros.student_id', '=', 'students.id')
                    ->join('folios', 'registros.folio_id', '=', 'folios.id')
                    ->join('schools', 'students.school_id', '=', 'schools.id')
                    ->whereBetween('students.created_at',[$saturday->format('Y-m-d'), $fecha1])
                    ->where('students.check', 'accepted')
                    ->where(function($query) {
                        $query->where('students.deleted_at', '!=', NULL )
                                ->orWhere('students.deleted_at', '=', NULL);
                    })
                    ->orderBy('registros.total', 'asc')->get();
        }
        if($this->state != 'accepted' && $this->state !== 'all_week') {
            $registros = \DB::table('registros')
                    ->select('students.created_at as fecha_registro', 'schools.name as school', 'students.name as name', 'students.book as book',
                        'students.numcuenta as numcuenta', 'registros.date as date', 'registros.type as type', 'registros.bank as bank', 'registros.invoice as invoice', 
                        'registros.auto as auto', 'registros.cajero as cajero', 'registros.total as total', 'registros.clave as fecha', 'registros.clave as concepto', 'registros.clave as abono')
                    ->join('students', 'registros.student_id', '=', 'students.id')
                    ->join('schools', 'students.school_id', '=', 'schools.id')
                    ->whereBetween('students.created_at',[$fecha1, $fecha2])
                    ->where('students.check', $this->state)
                    ->orderBy('registros.total', 'asc')->get();
        }
        
        return $registros;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        if($this->state == 'accepted') $title = 'ACEPTADOS';
        if($this->state == 'all_week') $title = 'ACEPTADOS (SABADO - VIERNES)';
        if($this->state == 'rejected') $title = 'RECHAZADOS';
        if($this->state == 'process') $title = 'EN PROCESO';
        return $title;
    }
}
