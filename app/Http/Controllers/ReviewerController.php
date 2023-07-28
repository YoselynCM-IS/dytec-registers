<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Registro;
use App\Student;
use App\School;
use App\Folio;
use App\Book;

class ReviewerController extends Controller
{
    public function home(){
        $hoy = Carbon::now()->format('Y-m-d');
        $students = Student::with('school')
            ->where('created_at', 'like', '%'.$hoy.'%')->orderBy('created_at', 'desc')->get();
        
        // $students = Student::with('school')->orderBy('created_at', 'desc')->get();
        return view('reviewer.home', compact('students'));
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
        
        return view('reviewer.codes', compact('digitales', 'fisicos'));
    }

    public function schools(){
        $schools = School::withCount(['books', 'students'])->orderBy('name', 'asc')->get();
        return view('reviewer.schools', compact('schools'));
    }

    public function books(){
        $books = Book::withCount('schools')->orderBy('name', 'asc')->get();
        return view('reviewer.books', compact('books'));
    }

    public function folios(){
        return view('reviewer.folios');
    }

    public function revisions(){
        return view('reviewer.revisiones.revisions');
    }

    public function categories(){
        return view('reviewer.revisiones.categories');
    }

    public function preregister(){
        $schools = \DB::table('schools')->orderBy('name', 'asc')->get();
        return view('reviewer.preregister', compact('schools'));
    }

    public function pagos(){
        return view('reviewer.revisiones.pagos');
    }
}
