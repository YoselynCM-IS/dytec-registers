<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\School;
use App\Book;

class BookController extends Controller
{
    public function store(Request $request){
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255', 'unique:books']
        ]);

        \DB::beginTransaction();
        try {
            $name = Str::of($request->name)->upper();
            $b = Book::create([
                'name' => Str::of($name)->upper(),
                'editorial' => $request->editorial
            ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        $book = Book::whereId($b->id)->first();
        return response()->json($book);
    }

    public function update(Request $request){
        \DB::beginTransaction();
        try {
            Book::whereId($request->id)->update([
                'name' => Str::of($request->name)->upper(),
                'editorial' => $request->editorial
            ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        $book = Book::whereId($request->id)->first();
        return response()->json($book);
    }

    public function assign_book(Request $request){
        \DB::beginTransaction();
        try {
            $book = Book::whereId($request->book_id)->first();
            $book->schools()->attach($request->school_id, ['price' => $request->price]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        
        return response()->json();
    }

    public function get_schools(Request $request){
        $book = Book::whereId($request->book_id)->with('schools')->first();
        $schools = $book->schools;
        return response()->json($schools);
    }

    public function show_books(Request $request){
        $books = Book::where('name','like', '%'.$request->book.'%')->with('schools')->get();
        return response()->json($books);
    }

    public function update_price(Request $request){
        \DB::beginTransaction();
        try {
            \DB::table('book_school')->where([
                'book_id' => $request->book_id, 'school_id' => $request->school_id
            ])->update(['price' => $request->price]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        $book = Book::whereId($request->book_id)->with('schools')->first();
        $schools = $book->schools;
        return response()->json($schools);
    }

    public function get_editoriales(){
        $editoriales = \DB::table('editoriales')->orderBy('name', 'asc')->get();
        return response()->json($editoriales);
    }

    public function delete(Request $request){
        \DB::beginTransaction();
        try {
            Book::whereId($request->book_id)->delete();
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        
        return response()->json();
    }
}
