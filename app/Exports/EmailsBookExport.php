<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Student;

class EmailsBookExport implements FromCollection
{
    protected $book;

    public function __construct($book)
    {
        $this->book = $book;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $students = Student::select('schools.name as school', 'students.name', 'email', 'book', 'students.updated_at')
                    ->join('schools', 'schools.id', '=', 'students.school_id')
                    ->where('book', $this->book)
                    ->where('check', 'accepted')
                    ->where('codes', true) 
                    ->orderBy('school', 'asc')->get(); 
        return $students;
    }
}
