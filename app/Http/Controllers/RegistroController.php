<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Exports\RegistroExport;
use App\Exports\RegistrosExport;
use App\Exports\ArchivoBExport;
use App\Exports\DayExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\PreRegister;
use Carbon\Carbon;
use App\Registro;
use App\Student;
use App\School;
use App\Folio;
use Excel;

class RegistroController extends Controller
{

    public function index(){
        $students = Student::with('school')->withCount('registros')->orderBy('created_at', 'desc')->get();
        return view('registros.home', compact('students'));
    }

    public function entregas(){
        $students = Student::where('check', 'accepted')->with('school')
                        ->orderBy('created_at', 'desc')->get();
        
        return view('registros.entregas', compact('students'));
    }

    public function revisions(){
        return view('registros.revisiones.revisions');
    }

    public function categories(){
        return view('registros.revisiones.categories');
    }

    public function schools(){
        $schools = School::withCount(['students'])->orderBy('name', 'asc')->get();
        return view('registros.schools', compact('schools'));
    }

    public function by_date(Request $request){
        $registros = Registro::select('student_id')->where('date', 'like', '%'.$request->fecha.'%')->groupBy('student_id')->get();
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('book', 'asc')->get();
        return response()->json($students);
    }

    public function by_type(Request $request){
        $registros = Registro::select('student_id')->where('type', $request->type)->groupBy('student_id')->get();
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function by_folio(Request $request){
        $registros = Registro::select('student_id')->where('invoice', $request->folio)->groupBy('student_id')->get();
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function by_total(Request $request){
        $registros = Registro::select('student_id')->where('total', (double)$request->total)->groupBy('student_id')->get();
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function by_book(Request $request){
        $students = Student::where('book', $request->book)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function download($temporal1){
        // if($temporal1 === "null") $temporal1 = "null";
        
        return Excel::download(new RegistroExport($temporal1), 'registros.xlsx');
    }

    public function download_status($status){
        return Excel::download(new RegistrosExport($status), 'registros.xlsx');
    }

    public function update_status(){
        $students = Student::where('check', 'process')->with('registros')->limit(50)->get();
        
        \DB::beginTransaction();
        try {
            $estudiantes = array();
            foreach($students as $student){
                
                foreach ($student->registros as $registro) {
                    $pago = new Carbon($registro->date);
                    $hoy = Carbon::now()->format('Y-m-d');
                    $difference = $pago->diff($hoy)->days;

                    $folio = $this->validar_folio($registro);
                    
                    if($folio !== null){
                        $registro->update(['status' => 'accepted', 'folio_id' => $folio->id]);
                    } else {
                        if($difference > 0){
                            $registro->update(['status' => 'rejected']);
                        }
                    }
                    
                }

                $count_registros = Registro::where('student_id', $student->id)
                                            ->where('status', 'accepted')->count();
                
                if($count_registros === $student->registros->count()){
                    foreach ($student->registros as $registro) {
                        Folio::whereId($registro->folio_id)->update(['occupied' => 1]);
                    }
                    $student->update(['check' => 'accepted', 'validate' => 'NO ENVIADO']);

                    $message = '';
                    array_push($estudiantes, ['student' => $student, 'message' => $message]);
                } else {
                    $count_process = Registro::where('student_id', $student->id)
                            ->where('status', 'process')->count();
                    if($count_process === 0){
                        $student->update(['check' => 'rejected', 'validate' => 'NO ENVIADO']);
                    }
                }

            }
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        foreach ($estudiantes as $estudiante) {
            $s = Student::whereId($estudiante['student']['id'])->first();
            if($s->check == 'accepted'){
                Mail::to($s->email)->send(new PreRegister($estudiante['message'], $s));
                $s->update(['validate' => 'ENVIADO']);
            }
        }
        
        $students = Student::with('school')->withCount('registros')->orderBy('created_at', 'desc')->get();
        
        return response()->json($students);
    }

    public function validar_folio($registro){
        $folio = null;
        // PRACTICAJA
        if($registro->type === 'practicaja'){
            // PENDIENTE, COMPLETAR INFORMACIÓN CON EL ESTADO DE CUENTA
            $folio = Folio::where('fecha',$registro->date)
                    ->where('concepto','like','%DEPOSITO EFECTIVO PRACTIC%')
                    ->where('concepto','like','%FOLIO:'.$registro->invoice.'%')
                    ->where('abono','like','%'.$registro->total.'%')
                    ->where('occupied', 0)->first();
        }
        // VENTANILLA
        if($registro->type === 'ventanilla'){
            $invoice = ltrim($registro->invoice,0);
            $auto = ltrim($registro->auto,0);

            if(strlen($invoice) > 3 && strlen($auto) > 3){
                $folio = Folio::where('fecha',$registro->date)
                        ->where('concepto','like','%DEPOSITO EN EFECTIVO/000'.$invoice.''.$auto.'%')
                        ->where('abono','like','%'.$registro->total.'%')
                        ->where('occupied', 0)
                        ->first();
            }
        }
        // TRANFERENCIA
        if($registro->type === 'transferencia'){
            $folio = Folio::where('fecha',$registro->date)
                        ->where(function($query){
                            $query->where('concepto','like','%PAGO CUENTA DE TERCERO%')
                                    ->orWhere('concepto','like','%TRASPASO ENTRE CUENTAS%');
                        })
                        ->where('concepto','like','%'.$registro->invoice.'%')
                        ->where('abono','like','%'.$registro->total.'%')
                        ->where('occupied', 0)
                        ->first();
        }
        return $folio;
    }

    public function search_folio($bank, $registro){
        $banco = $bank;
        if($bank === 'BANCO AZTECA') $banco = 'AZTECA';
        if($bank === 'BANBAJIO') $banco = 'BAJIO';
        if($bank === 'CAJA POPULAR MEXICANA') $banco = 'CAJA POP MEX';

        $folio = null;
        $invoice = $registro->invoice;

        if($registro->auto == ''){
            if($banco === 'BANCOPPEL') $invoice = substr($registro->invoice,0,10);
            
            $folio = Folio::where('concepto','like','%'.$banco.'%')
                    ->where('fecha',$registro->date)
                    ->where('concepto','like','%'.$invoice.'%')
                    ->where('abono','like','%'.$registro->total.'%')
                    ->where('occupied', 0)
                    ->first();
        } else {
            $auto = substr($registro->auto,0,15);
        
            if($banco === 'BANCOPPEL'){
                $invoice = substr($registro->invoice,16,7);
                $auto = substr($registro->auto,0,10);
            }

            $folio = Folio::where('concepto','like','%'.$banco.'%')
                        ->where('fecha',$registro->date)
                        ->where('concepto','like','%'.$invoice.'%')
                        ->where('concepto','like','%'.$auto.'%')
                        ->where('abono','like','%'.$registro->total.'%')
                        ->where('occupied', 0)
                        ->first();
        }
        
        return $folio;
    }

    public function banks_equal($bank){
        return $bank == 'BANAMEX' || $bank == 'BANCO AZTECA' || $bank == 'BANCOPPEL' || 
                $bank == 'BANBAJIO' || $bank == 'BANORTE' || $bank == 'BANREGIO' ||
                $bank == 'CAJA POPULAR MEXICANA' || $bank == 'COMPARTAMOS' || $bank == 'HSBC' || 
                $bank == 'INBURSA' || $bank == 'SANTANDER' || $bank == 'SCOTIABANK';
    }

    public function banks_not_equal($bank){
        return $bank !== 'BANAMEX' && $bank !== 'BANCO AZTECA' && $bank !== 'BANCOPPEL' && 
                $bank !== 'BANBAJIO' && $bank !== 'BANORTE' && $bank !== 'BANREGIO' &&
                $bank !== 'CAJA POPULAR MEXICANA' && $bank !== 'COMPARTAMOS' && $bank !== 'HSBC' && 
                $bank !== 'INBURSA' && $bank !== 'SANTANDER' && $bank !== 'SCOTIABANK';
    }

    public function update_rejected(Request $request){
        $students = Student::where('check', 'rejected')->with('registros')->get();
        \DB::beginTransaction();
        try {
            $estudiantes = array();
            foreach($students as $student){
                
                foreach ($student->registros as $registro) {
                    $pago = new Carbon($registro->date);
                    $hoy = Carbon::now()->format('Y-m-d');

                    $folio = $this->validar_folio($registro);

                    // if($folio == null){
                    //     $folio = $this->validar_folio_2($registro);
                    // }   

                    if($folio !== null){
                        $registro->update(['status' => 'accepted', 'folio_id' => $folio->id]);
                    } else {
                        $registro->update(['status' => 'rejected']);
                    }
                        
                }

                $count_registros = Registro::where('student_id', $student->id)
                            ->where('status', 'accepted')->count();
                
                if($count_registros === $student->registros->count()){
                    foreach ($student->registros as $registro) {
                        Folio::whereId($registro->folio_id)->update(['occupied' => 1]);
                    }
                    $student->update(['check' => 'accepted', 'validate' => 'NO ENVIADO']);

                    $message = '';

                    array_push($estudiantes, ['student' => $student, 'message' => $message]);
                } else {
                    $count_process = Registro::where('student_id', $student->id)
                            ->where('status', 'process')->count();
                    if($count_process === 0){
                        $student->update(['check' => 'rejected']);
                    }
                }

            }
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        
        foreach ($estudiantes as $estudiante) {
            $s = Student::whereId($estudiante['student']['id'])->first();
            if($s->check == 'accepted'){
                Mail::to($s->email)->send(new PreRegister($estudiante['message'], $s));
                $s->update(['validate' => 'ENVIADO']);
            }
        }
        
        $students = Student::where('check', 'rejected')->with('school')->orderBy('created_at', 'desc')->get();

        return response()->json($students); 
    }

    public function by_status(Request $request){
        $students = Student::where('check', $request->status)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function by_student(Request $request){
        $students = Student::where('name', $request->student)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function resend_mail(Request $request){
        $student = Student::find($request->id);

        $message = '';
        Mail::to($student->email)->send(new PreRegister($message, $student));
        $student->update(['validate' => 'ENVIADO']);

        return response()->json();
    }

    public function by_bank(Request $request){
        if($request->bank !== 'OTRO'){
            $registros = Registro::select('student_id')->where('bank', $request->bank)->groupBy('student_id')->get();
        } else {
            $registros = Registro::select('student_id')->where('bank', 'NOT LIKE', 'BANCOMER')
                        ->where('bank','NOT LIKE','BANAMEX')->where('bank','NOT LIKE','BANCO AZTECA%')
                        ->where('bank','NOT LIKE','BANCOPPEL')->where('bank','NOT LIKE','BANBAJIO')
                        ->where('bank','NOT LIKE','BANORTE')->where('bank','NOT LIKE','BANREGIO')
                        ->where('bank','NOT LIKE','CAJA POPULAR MEXICANA')->where('bank','NOT LIKE','COMPARTAMOS')
                        ->where('bank','NOT LIKE','HSBC')->where('bank','NOT LIKE','INBURSA')
                        ->where('bank','NOT LIKE','SANTANDER')->where('bank','NOT LIKE','SCOTIABANK')
                        ->groupBy('student_id')->get();
        }
        $students = Student::whereIn('id',$registros)->with('school')->orderBy('created_at', 'desc')->get();
        return response()->json($students);
    }

    public function validar_folio_2($registro){
        $folio = null;

        $fecha_pago = $registro->date;
        $fecha_menos_dos = strtotime ( '-2 day' , strtotime ( $fecha_pago ) ) ;
        $fecha_menos_dos = date ( 'Y-m-d' , $fecha_menos_dos );

        $fecha_mas_dos = strtotime ( '+2 day' , strtotime ( $fecha_pago ) ) ;
        $fecha_mas_dos = date ( 'Y-m-d' , $fecha_mas_dos );

        // BANCOMER
        if($registro->bank == 'BANCOMER'){
            $numero_referencia = strlen($registro->invoice);
            // PRACTICAJA
            if($numero_referencia == 4){
                $folio = Folio::where('concepto','like','%DEPOSITO EFECTIVO PRACTIC%')
                    ->where('concepto','like','%FOLIO:'.$registro->invoice.'%')
                    ->where('abono','like','%'.$registro->total.'%')
                    ->where('occupied', 0)
                    ->whereBetween('fecha', [$fecha_menos_dos,$fecha_mas_dos])
                    ->first();
            }
            // VENTANILLA
            if($numero_referencia > 4 && $numero_referencia < 10){
                $invoice = ltrim($registro->invoice,0);
                $folio = Folio::where('concepto','like','%'.$invoice.'%')
                        ->where('abono','like','%'.$registro->total.'%')
                        ->where('occupied', 0)
                        ->where(function($query){
                            $query->where('concepto','like','%DEPOSITO EN EFECTIVO/0%')
                                    ->orWhere('concepto','like','%DEPOSITO POR CORRECCION/%');
                        })
                        ->whereBetween('fecha', [$fecha_menos_dos,$fecha_mas_dos])
                        ->first();
            }
            // TRANFERENCIA
            if($numero_referencia == 10){
                $folio = Folio::where(function($query){
                        $query->where('concepto','like','%PAGO CUENTA DE TERCERO%')
                                ->orWhere('concepto','like','%TRASPASO ENTRE CUENTAS%');
                    })
                    ->where('concepto','like','%'.$registro->invoice.'%')
                    ->where('abono','like','%'.$registro->total.'%')
                    ->where('occupied', 0)
                    ->whereBetween('fecha', [$fecha_menos_dos,$fecha_mas_dos])
                    ->first();
            }
        } else {
            $folio = $this->search_folio_2($registro->bank, $registro, $fecha_menos_dos, $fecha_mas_dos);
        }
        
        return $folio;
    }

    public function search_folio_2($bank, $registro, $fecha_menos_dos,$fecha_mas_dos){
        $banco = $bank;
        if($bank === 'BANCO AZTECA') $banco = 'AZTECA';
        if($bank === 'BANBAJIO') $banco = 'BAJIO';
        if($bank === 'CAJA POPULAR MEXICANA') $banco = 'CAJA POP MEX';

        $invoice = $registro->invoice;
        $auto = substr($registro->auto,0,15);
        
        if($banco === 'BANCOPPEL'){
            $invoice = substr($registro->invoice,16,7);
            $auto = substr($registro->auto,0,10);
        }

        $folio = Folio::where('concepto','like','%'.$banco.'%')
                        ->where('concepto','like','%'.$invoice.'%')
                        ->where('concepto','like','%'.$auto.'%')
                        ->where('abono','like','%'.$registro->total.'%')
                        ->where('occupied', 0)
                        ->whereBetween('fecha', [$fecha_menos_dos,$fecha_mas_dos])
                        ->first();
        return $folio;
    }

    public function down_banxico($inicio,$final){
        return Excel::download(new ArchivoBExport($inicio,$final), 'registros.xlsx');
    }

    public function by_day(Request $request){
        $fecha1 = new Carbon($request->fecha1);
        $fecha2 = new Carbon($request->fecha2);
        $status = ['accepted', 'rejected'];

        $nombre = $fecha1->format('d-m-Y').'_'.$fecha2->format('d-m-Y').'.xlsx';
        return Excel::download(new DayExport($fecha1, $fecha2, $status), $nombre);
    }
}
