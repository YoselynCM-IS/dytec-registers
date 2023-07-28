<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Student;

class DeliveryExport implements FromCollection
{
    // EXPORTAR REGISTROS ACEPTADOS QUE ESTEN PENDIENTES POR ENVIAR
    // O QUE YA HAYAN SIDO ENVIADOS
    protected $status;
    protected $school;
    protected $book;

    public function __construct($status, $school, $book)
    {
        $this->status = $status;
        $this->school = $school;
        $this->book = $book;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // OBTENER POR ESCUELA
        if($this->book == 'null'){
            if($this->status == 'entregado'){
                $students = $this->get_schools(true);
            }
            if($this->status == 'pendiente'){
                $students = $this->get_schools(false);
            }
        }
        // OBTENER POR LIBRO
        if($this->school == 'null'){
            if($this->status == 'entregado'){
                $students = $this->get_books(true);
            }
            if($this->status == 'pendiente'){
                $students = $this->get_books(false);
            }
        }
        return $students;
    }

    public function get_schools($estado){
        $s = $this->school;
        return Student::select('schools.name as school', 'students.name', 'email', 'book')
                        ->join('schools', 'schools.id', '=', 'students.school_id')
                        ->where(function($query) use($s,$estado) {
                            $query->where('schools.name', $s)
                                    ->where('check', 'accepted')
                                    ->where('book', 'like', '%DIGITAL%')
                                    ->where('book', 'NOT LIKE', '%PACK%')
                                    ->where('codes', $estado);
                        })
                        ->orWhere(function($query) use($s,$estado) {
                            $query->where('schools.name', $s)
                                    ->where('check', 'accepted')
                                    ->where(function($query) {
                                        $query->where('book', 'NOT LIKE', '%DIGITAL%')
                                                ->orWhere('book', 'like', '%PACK%');
                                    })->where('delivery', $estado);
                        })
                        ->orderBy('students.created_at', 'asc')->get();
    }

    public function get_books($estado){
        $b = $this->book;
        return Student::select('schools.name as school', 'students.name', 'email', 'book')
                        ->join('schools', 'schools.id', '=', 'students.school_id')
                        ->where(function($query) use($b,$estado) {
                            $query->where('book', $b)
                                    ->where('book', 'like', '%DIGITAL%')
                                    ->where('book', 'NOT LIKE', '%PACK%')
                                    ->where('check', 'accepted')
                                    ->where('codes', $estado);
                        })
                        ->orWhere(function($query) use($b,$estado) {
                            $query->where('book', $b)
                                    ->where(function($query) {
                                        $query->where('book', 'NOT LIKE', '%DIGITAL%')
                                                ->orWhere('book', 'like', '%PACK%');
                                    })->where('check', 'accepted')
                                    ->where('delivery', $estado);
                        })
                        ->orderBy('students.created_at', 'asc')->get();
    }
}
