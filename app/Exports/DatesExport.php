<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DatesExport implements FromCollection,WithHeadings
{
    // EXPORTAR CODIGOS ENVIADOS / LIBROS ENTREGADOS POR FECHA
    protected $type;
    protected $inicio;
    protected $final;

    public function __construct($type, $inicio, $final)
    {
        $this->type = $type;
        $this->inicio = $inicio;
        $this->final = $final;
    }

    public function headings(): array
    {
        return [
            'FECHA DE ENVIO /ENTREGA', 
            'REGISTRADO POR',
            'ESCUELA',
            'ALUMNO',
            'CORREO ELECTRÓNICO',
            'LIBRO'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->type == 'digital'){
            $students = \DB::table('students')->select('date_codes', 'user_codes', 'schools.name as school', 'students.name', 'email', 'book')
                        ->join('schools', 'students.school_id', '=', 'schools.id')
                        ->whereBetween('date_codes',[$this->inicio, $this->final])
                        ->orderBy('date_codes', 'asc')
                        ->get();
        }
        if($this->type == 'fisico') {
            $students = \DB::table('students')->select('date_delivery', 'user_delivery', 'schools.name as school', 'students.name', 'email', 'book')
                        ->join('schools', 'students.school_id', '=', 'schools.id')
                        ->whereBetween('date_delivery',[$this->inicio, $this->final])
                        ->orderBy('date_delivery', 'asc')
                        ->get();
        }
        return $students;
    }
}
