<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Student;

class CorteCategorieExport implements FromView
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $students = $this->get_registros();
        return view('download.registros-corte', [
            'students' => $students
        ]);
    }

    public function get_registros()
    {
        $students = Student::where('categorie_id', $this->id)
            ->withTrashed()->with('school', 'registros.folio')
            ->orderBy('created_at', 'desc')->get();
        
        return $students;
    }
}

