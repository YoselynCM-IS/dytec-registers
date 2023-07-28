<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Student;
use App\School;
use App\Folio;
use App\File;
use App\Book;

class ManagerController extends Controller
{
    // MOSTRAR Y SUBIR FOLIOS
    public function folios(){
        return view('manager.folios');
    }

    public function files(){
        $files = File::orderBy('created_at', 'desc')->get();
        return view('manager.files', compact('files'));
    }

    public function home(){
        $hoy = Carbon::now()->format('Y-m-d');
        $students = Student::where('created_at', 'like', '%'.$hoy.'%')
                        ->with('school')->withCount('registros')->orderBy('created_at', 'desc')->get();
        return view('manager.home', compact('students'));
    }

    public function codes(){
        $hoy = Carbon::now()->format('Y-m-d');
        $digitales = Student::where('check', 'accepted')
                ->where('created_at', 'like', '%'.$hoy.'%')
                ->where(function($query) {
                    $query->where('book', 'like', '%DIGITAL%')
                            ->orWhere('book', 'like', '%PACK%');
                })->with('school')->orderBy('book', 'asc')->get();
        $fisicos = Student::where('check', 'accepted')
                ->where('created_at', 'like', '%'.$hoy.'%')
                ->where('book', 'NOT LIKE', '%DIGITAL%')
                ->with('school')->orderBy('book', 'asc')->get();
        
        return view('manager.codes', compact('digitales', 'fisicos'));
    }

    public function schools(){
        $schools = School::withCount(['books', 'students'])->orderBy('name', 'asc')->get();
        return view('manager.schools', compact('schools'));
    }

    public function books(){
        $books = Book::withCount('schools')->orderBy('name', 'asc')->get();
        return view('manager.books', compact('books'));
    }

    public function movimientos(){
        return view('manager.movimientos');
    }

    public function revisions(){
        return view('manager.revisiones.revisions');
    }

    public function categories(){
        return view('manager.revisiones.categories');
    }

    public function pagos(){
        return view('manager.revisiones.pagos');
    }
}
