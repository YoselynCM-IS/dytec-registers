<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmailsBookExport;
use App\Exports\DeliveryExport;
use App\Mail\ErrorPreregister;
use App\Exports\EmailsExport;
use App\Exports\DatesExport;
use App\Imports\CodesImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Dropbox\Client;
use App\Mail\PreRegister;
use App\Mail\SendCodes;
use App\Comprobante;
use Carbon\Carbon;
use App\Registro;
use App\Student;
use App\School;
use App\Folio;
use App\Book;
use App\Code;

class StudentController extends Controller
{

    public function __construct()
    {
        // Necesitamos obtener una instancia de la clase Client la cual tiene algunos métodos
        // que serán necesarios.
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();   
    }

    // VISTA AL REGISTRO DEL ALUMNO
    public function register(){
        $schools = \DB::table('schools')->orderBy('name', 'asc')->get();
        return view('student.register', compact('schools'));
    }

    // GUARDAR ESTUDIANTE
    public function store(Request $request){
        // VALIDACIÓN DE DATOS
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3'],
            'lastname' => ['required', 'string', 'min:3'],
            'lastname2' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', 'max:60'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'telephone' => ['required', 'numeric', 'min:1000000000'],
            'file' => ['required', 'mimes:jpg,png,jpeg,pdf', 'max:5072'],
            'numcuenta' => ['required', 'numeric', 'min:6']
        ]);
        
        // BUSCAR SI EXISTE EL ALUMNO
        $name = Str::of(trim($request->name).' '.trim($request->lastname).' '.trim($request->lastname2))->upper();
        
        $verificar = Student::where('name', 'like', '%'.$name.'%')
                                ->where('book', Str::of($request->book)->upper())
                                ->first();
        
        if($verificar !== null && ($verificar->check === 'process' || ($verificar->check === 'rejected' && $verificar->validate === 'NO ENVIADO'))) return response()->json(1);
        if($verificar !== null && $verificar->check === 'accepted') return response()->json(2);
        
        // NO EXISTE O ESTA RECHAZADO
        if($verificar === null || ($verificar->check === 'rejected' && $verificar->validate === 'ENVIADO')){
            try {
                \DB::beginTransaction();

                $student = Student::create([
                    'school_id' => $request->school_id,
                    'name' => $name,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'book' => Str::of($request->book)->upper(), 
                    'quantity' => (int)$request->quantity, 
                    'price' => (float)$request->price,
                    'total' => (float)$request->a_depositar,
                    'validate' => 'CREADO',
                    // 'teacher' => Str::of($request->teacher)->upper(),
                    'numorden' => $request->orden,
                    'numcuenta' => $request->numcuenta
                ]);
                
                // COMPROBANTES DE PAGO
                $comprobantes = json_decode($request->comprobantes);
                foreach($comprobantes as $comprobante) {
                    $datos = $this->set_types($comprobante->type, $comprobante->bank, $comprobante->folio, $comprobante->auto);

                    $registro = Registro::create([
                        'student_id' => $student->id, 
                        'type' => $datos['type'], 
                        'invoice' => $datos['invoice'], 
                        'auto' => $datos['auto'], 
                        'clave' => Str::of($comprobante->clave)->upper(),
                        'total' => (float)$comprobante->total, 
                        'date' => $comprobante->date, 
                        'plaza' => Str::of($comprobante->plaza)->upper(),
                        'bank' => $datos['bank'],
                        'cajero' => $comprobante->cajero
                    ]);
                }

                // GUARDAR IMAGEN DE COMPROBANTE
                $file = $request->file('file');

                $name_student = Str::slug($name, '-');
                $extension = $file->getClientOriginalExtension();
                $name_file = time()."_id".$student->id."_".$name_student.".".$extension;

                Storage::disk('dropbox')->putFileAs(
                    '/dytec/comprobantes/', $request->file('file'), $name_file
                );
    
                $response = $this->dropbox->createSharedLinkWithSettings(
                    '/dytec/comprobantes/'.$name_file, 
                    ["requested_visibility" => "public"]
                );

                Comprobante::create([
                    'student_id' => $student->id,
                    'name' => $response['name'],
                    'extension' => $extension,
                    'size' => $response['size'],
                    'public_url' => $response['url']
                ]);

                \DB::commit();

            }  catch (Exception $e) {
                \DB::rollBack();
                $success = $exception->getMessage();
            }
        }
        return response()->json(3);
    }

    // ASIGNAR VARIABLES
    public function set_types($type, $bank, $folio, $auto){
        $bank = $bank;
        
        if($type === 'oxxo')
            $bank = 'BANAMEX EXTERNA';
        if($type === 'practicaja' || $type === 'ventanilla') 
            $bank = 'BANCOMER';
        if($type === 'transferencia') 
            $bank = $bank;
        if($type === 'BANCO AZTECA') 
            $bank = 'BANCO AZTECA';

        $invoice = $folio;
        $auto = $auto;

        if($bank === 'BANCOPPEL') 
            $invoice = Str::of($folio)->upper();
        if($type === 'practicaja') 
            $auto = Str::of($auto)->upper();
        if($type === 'ventanilla') 
            $auto = 'N/A';

        return [
            'type' => $type,
            'bank' => $bank,
            'invoice' => $invoice,
            'auto' => $auto
        ];
    }

    // CONSULTAR REGISTROS DE STUDENT
    public function show_registers(Request $request){
        $student = Student::whereId($request->student_id)
                ->with('registros.folio', 'comprobantes', 'school')
                ->withTrashed()
                ->first();
        return response()->json($student);
    }

    public function consult_data($date, $id){
        $student = Student::where(['id' => $id, 'created_at' => $date])
                    ->with('registro.comprobante', 'school')->first();
        return view('student.consult', compact('student'));
    }

    public function download_emails($school, $book){
        $hoy = Carbon::now()->format('Y-m-d');
        $nombre_archivo = $hoy.'_envio-codigos.xlsx';
        return Excel::download(new EmailsExport($school, $book), $nombre_archivo);
    }

    public function download_delivery($status, $school, $book){
        $nombre_archivo = $status.'-fisico-digital.xlsx';
        return Excel::download(new DeliveryExport($status, $school, $book), $nombre_archivo);
    }

    public function send_codes(Request $request){
        $array = Excel::toArray(new CodesImport, request()->file('file'));
        
        try {
            $countS = [];
            $lista = collect($array[0]);
            $lista->map(function($row) use(&$countS){
                $name = $row[3];
                $e = $row[2];
                $code1 = $row[5];
                $code2 = $row[6];
                $code3 = $row[7];

                if($e === 'MAJESTIC EDUCATION' || $e === 'EXPRESS PUBLISHING' 
                    || $e === 'CENGAGE' || $e === 'RICHMOND' || $e === 'CLE'){
                    if(strlen($code1) > 0 && strlen($code2) > 0 && strlen($code3) > 0){
                        $student = Student::where([
                            'name' => $name, 'email' => $row[4], 
                            'check' => 'accepted', 'book' => $row[1],
                            'codes' => false,
                        ])->first();
                        
                        if($student !== null){
                            Code::create([
                                'student_id' => $student->id, 
                                'editorial' => $e, 
                                'code1' => $code1, 
                                'code2' => $code2, 
                                'code3' => $code3
                            ]);
    
                            // $name, $code, $code2, $code3, $book, $editorial
                            Mail::to($student->email)->send(new SendCodes($student->name, $code1, $code2, $code3, $student->book, $e));
    
                            $student->update([
                                'send_codes' => $student->send_codes + 1
                            ]);
    
                            if($student->send_codes == $student->quantity){
                                $student->update([
                                    'codes'     => true,
                                    'date_codes' => Carbon::now()->format('Y-m-d h:m:s'),
                                    'user_codes' => auth()->user()->name
                                ]);
                            }
                        } else {
                            if(strlen($name) > 0){
                                $countS[] = $name;
                            }
                        } 
                    } else {
                        if(strlen($name) > 0){
                            $countS[] = $name;
                        }
                    }
                } else {
                    if(strlen($name) > 0){
                        $countS[] = $name;
                    }
                }
            });
        }  catch (Exception $e) {
            $success = $exception->getMessage();
        }
        
        return response()->json($countS);
    }

    public function books_to_email(Request $request){
        $students = Student::where('book', $request->book)
                    ->where('check', 'accepted')->with('school')
                    ->orderBy('created_at', 'asc')->get();
        return response()->json($students);
    }

    public function delete(Request $request){
        $student = Student::whereId($request->student_id)->delete();
        $students = Student::where('check', 'rejected')->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }
    
    public function download_tutorial(){
        return Storage::disk('dropbox')->download('/PRE-REGISTRO-DE-BAUCHERS.pdf');
    }

    public function show_students(Request $request){
        $students = Student::select('name')
                ->where('name','like', '%'.$request->student.'%')
                ->groupBy('name')->get();
        return response()->json($students);
    }

    public function update_status(Request $request){
        $student = Student::whereId($request->student_id)->first();
        
        \DB::beginTransaction();
        try {
            foreach ($request->registros as $r){
                $registro = Registro::whereId($r['id'])->first();
                $folio = Folio::whereId($r['folio_id'])->first();
                
                $registro->update(['status' => 'accepted', 'folio_id' => $folio->id]);
                $folio->update(['occupied' => 1]);
            }

            $student->update(['check' => 'accepted', 'validate' => 'NO ENVIADO']);

            $message = '';

            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        if($student->check !== 'rejected'){
            Mail::to($student->email)->send(new PreRegister($message, $student));
            $student->update(['validate' => 'ENVIADO']);
        }

        $hoy = Carbon::now()->format('Y-m-d');
        $students = Student::with('school')
                ->where('created_at', 'like', '%'.$hoy.'%')
                ->orderBy('created_at', 'desc')->get();

        return response()->json($students);
    }

    public function update_delivery(Request $request){
        \DB::beginTransaction();
        try {
            $student = Student::whereId($request->id)->update([
                    'delivery' => true,
                    'date_delivery' => Carbon::now()->format('Y-m-d h:m:s'),
                    'user_delivery' => auth()->user()->name
                ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        if(auth()->user()->role == 'capturist'){
            $students = Student::where('school_id', $request->school_id)
                        ->where('check', 'accepted')
                        ->where('delivery', false)
                        ->where('book','NOT LIKE','%DIGITAL%')
                        ->with('school')->orderBy('name', 'asc')->get();
        } else {
            $students = Student::with('school')
                ->orderBy('created_at', 'desc')->where('check', 'accepted')->get();
        }
        return response()->json($students);
    }

    public function by_school(Request $request){
        $students = Student::where('school_id', $request->school_id)
                        ->where('name','like', '%'.$request->student.'%')
                        ->where('check', 'accepted')
                        ->where('delivery', false)
                        ->where('book','NOT LIKE','%DIGITAL%')
                        ->with('school')->orderBy('name', 'asc')->get();

        return response()->json($students);
    }

    public function by_school_ne(Request $request){
        $students = Student::where('name','like', '%'.$request->student.'%')
                        ->where('check', 'accepted')
                        ->with('school')->orderBy('created_at', 'asc')->get();

        return response()->json($students);
    }

    public function mark_delivery(Request $request){
        $selected = $request->selected;

        \DB::beginTransaction();
        try {
            foreach ($selected as $sel) {
                if(!$sel['delivery']) {
                    $student = Student::whereId($sel['id'])->update([
                        'delivery' => true,
                        'date_delivery' => Carbon::now()->format('Y-m-d h:m:s'),
                        'user_delivery' => auth()->user()->name
                    ]);
                }
            }
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
    
        return response()->json();
    }

    public function down_by_book($book){
        return Excel::download(new EmailsBookExport($book), 'correos.xlsx');
    }

    public function debug_accepted(){
        \DB::beginTransaction();
        try {
            $accepted = Student::select('name', 'book')->where('check', 'accepted')->get();
            
            $names = array();
            $books = array();
            foreach ($accepted as $accept) {
                array_push($names, $accept->name);
                array_push($books, $accept->book);
            }

            $num_debug = Student::whereIn('name', $names)
                            ->whereIn('book', $books)
                            ->where('check', 'rejected')->count();
            Student::whereIn('name', $names)
                    ->whereIn('book', $books)
                    ->where('check', 'rejected')->delete();
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        $students = Student::where('check', 'rejected')->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json(['num_debug' => $num_debug, 'students' => $students]);
    }

    public function update_preregister(Request $request){
        $this->validate($request, [
            'name' => ['required', 'string', 'min:6'],
            'email' => ['required', 'email', 'max:60'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'telephone' => ['required', 'numeric', 'min:1000000000']
        ]); 

        $s = Student::find($request->id);
        \DB::beginTransaction();
        try {
            $s->update([
                'school_id' => $request->school,
                'name' => Str::of($request->name)->upper(),
                'email' => $request->email,
                'telephone' => $request->telephone,
                'book' => Str::of($request->book)->upper(), 
                'quantity' => (int)$request->quantity, 
                'price' => (float)$request->price,
                'total' => (float)$request->a_depositar
            ]);

            $comprobantes = collect($request->comprobantes);
        
            $comprobantes->map(function($comprobante){
                $datos = $this->set_types($comprobante['type'], $comprobante['bank'], $comprobante['invoice'], $comprobante['auto']);
                $registro = Registro::find($comprobante['id']);

                $r_datos = [
                    'student_id' => $comprobante['student_id'],
                    'type' => $datos['type'], 
                    'invoice' => $datos['invoice'], 
                    'auto' => $datos['auto'], 
                    'total' => (float)$comprobante['total'], 
                    'date' => $comprobante['date'], 
                    'bank' => $datos['bank']
                ];

                if($registro == null) {
                    Registro::create($r_datos);
                } else {
                    $registro->update($r_datos);
                }
            });
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        $student = Student::whereId($s->id)->with('registros.folio', 'comprobantes', 'school')->first();
        return response()->json($student);
    }

    public function codes_dates(Request $request){
        $digitales = Student::whereBetween('date_codes',[$request->inicio, $request->final])
                    ->with('school')->orderBy('date_codes', 'asc')->get();
        return response()->json($digitales);
    }

    public function delivery_dates(Request $request){
        $fisicos = Student::whereBetween('date_delivery',[$request->inicio, $request->final])
                    ->with('school')->orderBy('date_delivery', 'asc')->get();
        return response()->json($fisicos);
    }

    public function download_dates($type, $inicio, $final){
        return Excel::download(new DatesExport($type, $inicio, $final), 'enviados-por-fecha.xlsx');
    }

    public function send_error(Request $request){
        $error = print_r($request->error, true);
        return response()->json($error);
    }
}
