<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Exports\CorteCategorieExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Dropbox\Client;
use Carbon\Carbon;
use App\Categorie;
use App\Registro;
use App\Student;
use App\Pago;

class RevisionController extends Controller
{
    public function __construct()
    {
        // Necesitamos obtener una instancia de la clase Client la cual tiene algunos métodos
        // que serán necesarios.
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();   
    }
    
    public function index(){
        $students = Student::where('check', 'accepted')
                    ->where(function($query) {
                        $query->where('codes', true)
                                ->orWhere('delivery', true);
                    })
                    ->where('reviewed', false)
                    ->with('school')
                    ->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json($students);
    }

    // CREAR CATEGORIA
    public function save_categorie(Request $request){
        \DB::beginTransaction();
        try {
            $hoy = Carbon::now()->format('Y-m-d h:m:s');
            Categorie::create([
                    'school_id' => $request->school_id,
                    'categorie' => Str::of($request->categorie)->upper(),
                    'creado_por' => auth()->user()->name,
                    'created_at' => $hoy,
                    'updated_at' => $hoy
                ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json();
    }

    // ACTUALIZAR CATEGORIE
    public function update_categorie(Request $request){
        \DB::beginTransaction();
        try {
            $hoy = Carbon::now()->format('Y-m-d h:m:s');
            $categorie = Categorie::where('id', $request->id)->update([
                            'categorie' => Str::of($request->categorie)->upper(),
                            'creado_por' => auth()->user()->name,
                            'updated_at' => $hoy
                        ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json($categorie);
    }

    // ELIMINAR CATEGORIA
    public function delete_categorie(Request $request){
        \DB::beginTransaction();
        try {
            $categorie = Categorie::where('id', $request->categorie_id)->delete();
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json(true);
    }

    // OBTENER CATEGORIAS
    public function show_categories(){ 
        $categories = Categorie::where('archivado', 0)
            ->orderBy('created_at', 'desc')->get();
        return response()->json($categories);
    }

    // OBTENER CATEGORIAS POR ESCUELA
    public function categories_byschool(Request $request){
        $categories = Categorie::where('school_id', $request->school_id)->where('archivado', 0)
                    ->orderBy('created_at', 'desc')->get();
        return response()->json($categories);
    }

    // MOVER REGISTROS
    public function save(Request $request){
        \DB::beginTransaction();
        try {
            $hoy = Carbon::now()->format('Y-m-d h:m:s');
            $categorie_id = $request->categorie_id;
            $selected = collect($request->selected);

            $selected->map(function($select) use($categorie_id, $hoy){
                $student = Student::find($select['id']);
                $student->update([
                    'categorie_id'  => $categorie_id,
                    'reviewed' => true, 
                    'date_reviewed' => $hoy
                ]);
                $student->delete();
            });

            Categorie::where('id', $categorie_id)
                ->update(['creado_por' => auth()->user()->name]);

            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json();
    }

    public function show(Request $request){
        $students = Student::where('check', 'accepted')
                    ->where('school_id', $request->school_id)
                    ->where(function($query) {
                        $query->where('codes', true)
                                ->orWhere('delivery', true);
                    })->where('reviewed', false)
                    ->with('school')
                    ->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($students);
    }

    public function by_categorie(Request $request){
        $students = Student::where('categorie_id', $request->categorie_id)
                    ->with('school')->withTrashed()
                    ->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($students);
    }

    public function by_student(Request $request){
        $students = Student::where('categorie_id', $request->categorie_id)
            ->where('name', 'like', '%'.$request->student.'%')
            ->withTrashed()->get();
        return response()->json($students);
    }

    public function show_student(Request $request){
        $student = Student::whereId($request->student_id)
                ->with('school')->withTrashed()
                ->paginate(1);
        return response()->json($student);
    }

    public function download_categorie($id){
        $hoy = Carbon::now()->format('Y-m-d');
        $nombre_archivo = $hoy.'-corte.xlsx';
        return Excel::download(new CorteCategorieExport($id), $nombre_archivo);
    }

    public function archive_categorie(Request $request){
        \DB::beginTransaction();
        try {
            Categorie::where('id', $request->categorie_id)
                ->update(['archivado' => 1]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json(true);
    }

    public function calculate_libros(Request $request){
        $students = Student::whereIn('categorie_id', $request->ids)
                            ->withTrashed()->get();
        $totales = [
            'total_libros' => $students->sum('quantity'),
            'total' => $students->sum('total')
        ];
        return response()->json($totales);
    }

    public function save_pago(Request $request){
        \DB::beginTransaction();
        try {
            // SUBIR IMAGEN
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $name_file = time().".".$extension;

            $image = Image::make($request->file('file'));
            $image->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            Storage::disk('dropbox')->put(
                '/comisiones/'.$name_file, (string) $image->encode('jpg', 30)
            );
            
            $response = $this->dropbox->createSharedLinkWithSettings(
                '/comisiones/'.$name_file, 
                ["requested_visibility" => "public"]
            );

            $school_id = $request->school_id;
            $pago = Pago::create([
                'school_id' => $school_id, 
                'total_comision' => $request->total_comision, 
                'total_libros' => $request->total_libros, 
                'comision_libro' => (float) $request->comision_libro,
                'name' => $response['name'], 
                'size' => $response['size'], 
                'extension' => $extension, 
                'public_url' => $response['url']
            ]);

            $idscortes = collect($request->ids);
            $idscortes->map(function($id) use($pago, $school_id){
                \DB::table('categorie_pago_school')->insert([
                    'categorie_id' => $id,
                    'pago_id' => $pago->id,
                    'school_id' => $school_id
                ]);

                Categorie::where('id', $id)
                    ->update(['archivado' => 1]);
            });
        \DB::commit();

        }  catch (Exception $e) {
            \DB::rollBack();
            $success = $exception->getMessage();
        }
        return response()->json(true);
    }

    public function get_pagos(){
        $pagos = Pago::orderBy('created_at', 'desc')->with('school')->paginate(20);
        return response()->json($pagos);
    }
}
