<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Student;
use App\Folio;
use App\File;

class AdministratorController extends Controller
{
    // VISTA DE LOS MOVIMIENTOS
    public function movimientos(){
        return view('administrator.movimientos');
    }

    public function folios(){
        return view('administrator.folios');
    }

    public function home(){
        $hoy = Carbon::now()->format('Y-m-d');
        $students = Student::with('school')
            ->where('created_at', 'like', '%'.$hoy.'%')->orderBy('created_at', 'desc')->get();
        
        return view('administrator.home', compact('students'));
    }
}
