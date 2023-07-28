<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Student;

class RegistrosExport implements FromCollection
{
    protected $status;
    
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return \DB::table('students')->select('check', 'students.id', 'students.name', 'schools.name as escuela', 'book', 'quantity', 'price', 'total', 'email', 'telephone')
                ->join('schools', 'students.school_id', '=', 'schools.id')
                ->where(['check' => $this->status, 'deleted_at' => null])->orderBy('students.created_at', 'desc')->get();
    }
}
