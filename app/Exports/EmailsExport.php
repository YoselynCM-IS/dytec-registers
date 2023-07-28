<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Student;

class EmailsExport implements FromCollection
{
    // EXPORTAR REGISTROS PENDIENTES DE ENVIAR CODIGO
    protected $school;
    protected $book;

    public function __construct($school, $book)
    {
        $this->school = $school;
        $this->book = $book;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->book === 'null'){
            // BUSQUEDA POR ESCUELA
            $stdspart1 = Student::select('schools.name as school', 'book', 'books.editorial', 'students.name', 'email')
                ->join('schools', 'schools.id', '=', 'students.school_id')
                ->join('books', 'students.book', '=', 'books.name')
                ->where('check', 'accepted')
                ->where('codes', false) 
                ->where('schools.name', $this->school);

            if($this->school == 'INSTITUTO TECNOLÓGICO DE VALLE DE ETLA'){
                $students = $stdspart1->where(function($query) {
                                $query->where('book', 'like', '%DIGITAL%')
                                        ->orWhere('book', 'like', '%PACK%');
                                })->orderBy('students.created_at', 'asc')->get();
            } else {
                $students = $stdspart1->where('book', 'like', '%DIGITAL%')
                                    ->where('book', 'NOT LIKE', '%PACK%')
                                    ->orderBy('students.created_at', 'asc')->get();
            }
        }
        if($this->school === 'null'){
            // BUSQUEDA POR LIBRO
            $students = Student::select('schools.name as school', 'book', 'books.editorial', 'students.name', 'email')
                ->join('schools', 'schools.id', '=', 'students.school_id')
                ->join('books', 'students.book', '=', 'books.name')
                ->where('book', 'like', '%DIGITAL%')
                ->where('book', 'NOT LIKE', '%PACK%') 
                ->where('check', 'accepted')
                ->where('codes', false)
                ->where('book', $this->book)
                ->orderBy('students.created_at', 'asc')->get(); 
        } 
        
        return $students;
    }
}
