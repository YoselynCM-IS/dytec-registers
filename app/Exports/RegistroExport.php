<?php

namespace App\Exports;
use App\Registro;
use App\Student;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RegistroExport implements FromView
{
    protected $temporal1;
    
    public function __construct($temporal1)
    {
        $this->temporal1 = $temporal1;
    }

    public function view(): View
    {
        $students = $this->get_registros();
        return view('download.registros', [
            'students' => $students
        ]);
    }

    public function get_registros(){
        // if($this->temporal1 === 'null'){
        //     $students = Student::with('school')->orderBy('book', 'asc')->get();
        // } else {
            // if($this->temporal1 === 'school'){
                $students = Student::where('school_id', $this->temporal1)
                                    ->where('check', 'accepted')
                                    ->with('school')->orderBy('name', 'asc')->get();
            // }
            // if($this->temporal1 === 'date'){
            //     $registros = Registro::select('student_id')->where('date', 'like', '%'.$this->temporal2.'%')->groupBy('student_id')->get();
            //     $students = Student::whereIn('id',$registros)->with('school')->orderBy('book', 'asc')->get();
            // }
            // if($this->temporal1 === 'book'){
            //     $students = Student::where('book', $this->temporal2)->with('school')->orderBy('created_at', 'desc')->get();
            // }
        // }
        return $students;
    }
}
