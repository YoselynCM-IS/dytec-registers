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
use App\Folio;
use Excel;

class RegistroController extends Controller
{
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
        $registros = Registro::select('student_id')->where('total', $request->total)->groupBy('student_id')->get();
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
        $students = Student::where('check', 'process')->with('registros')->limit(25)->get();
        
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

                    $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente.';
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
                if($student->school_id != 1){ // NO ENVIAR CORREO A LOS ALUMNOS DE CAMPECHE
                    Mail::to($s->email)->send(new PreRegister($estudiante['message'], $s));
                    $s->update(['validate' => 'ENVIADO']);
                }
            }
        }
        
        $hoy = Carbon::now()->format('Y-m-d');
        $students = Student::where('created_at', 'like', '%'.$hoy.'%')->with('school')->orderBy('created_at', 'desc')->get();

        return response()->json($students);
    }

    public function validar_folio($registro){
        $folio = null;
        // PRACTICAJA
        if($registro->type === 'practicaja'){
            $fpart1 = Folio::where('concepto','like','%DEPOSITO EFECTIVO PRACTIC%')
                    ->where('fecha',$registro->date)
                    ->where('concepto','like','%FOLIO:'.$registro->invoice.'%')
                    ->where('abono','like','%'.$registro->total.'%')
                    ->where('occupied', 0);
            if($registro->student->numcuenta == '0189525114'){
                $folio = $fpart1->where('concepto','like','%**5114%')->first();
            } else {
                $folio = $fpart1->where('concepto','like','%**7206%')->first();
            }
        }
        // VENTANILLA
        if($registro->type === 'ventanilla'){
            $invoice = ltrim($registro->invoice,0);

            $fpart1 = Folio::where('fecha',$registro->date)
                    ->where('abono','like','%'.$registro->total.'%')
                    ->where('occupied', 0);

            if($registro->student->numcuenta == '0189525114'){
                $auto = ltrim($registro->auto,0);
                if(strlen($invoice) > 3 && strlen($auto) > 3){
                    $folio = $fpart1->where('concepto','like','%DEPOSITO EN EFECTIVO/000'.$invoice.''.$auto.'%')->first();
                }
            } else {
                if(strlen($invoice) > 3){
                    // if($registro->invoice !== 'deposito' && $registro->invoice !== 'deposito en efectivo'){
                        $folio = $fpart1->where('concepto','like','%DEPOSITO EN EFECTIVO/0'.$invoice.'%')->first();
                        // ->where(function($query){
                        //     $query->where('concepto','like','%DEPOSITO EN EFECTIVO/0%')
                        //             ->orWhere('concepto','like','%DEPOSITO POR CORRECCION/%');
                        // })
                    // }
                }
            }
        }
        // TRANFERENCIA
        if($registro->type === 'transferencia'){
            $bank = $registro->bank;
            // BANCOMER
            if($bank === 'BANCOMER') {
                $folio = Folio::where('fecha',$registro->date)
                    ->where(function($query){
                        $query->where('concepto','like','%PAGO CUENTA DE TERCERO%')
                                ->orWhere('concepto','like','%TRASPASO ENTRE CUENTAS%');
                    })
                    ->where('concepto','like','%'.$registro->invoice.'%')
                    ->where('abono','like','%'.$registro->total.'%')
                    ->where('occupied', 0)
                    ->first();
            } else { // LOS DEMAS INCLUSO OTROS
                if($this->banks_equal($bank)){
                    $folio = $this->search_folio($bank, $registro);
                }
                if($this->banks_not_equal($bank)){
                    $invoice = $registro->invoice;
                    $auto = substr($registro->auto,0,15);

                    $folio = Folio::where('concepto','NOT LIKE','%DEPOSITO EFECTIVO PRACTIC%')
                                ->where('concepto','NOT LIKE','%DEPOSITO EN EFECTIVO/0%')
                                ->where('concepto','NOT LIKE','%DEPOSITO POR CORRECCION/%')
                                ->where('concepto','NOT LIKE','%PAGO CUENTA DE TERCERO%')
                                ->where('concepto','NOT LIKE','%BANAMEX%')->where('concepto','NOT LIKE','%AZTECA%')
                                ->where('concepto','NOT LIKE','%BANCOPPEL%')->where('concepto','NOT LIKE','%BAJIO%')
                                ->where('concepto','NOT LIKE','%BANORTE%')->where('concepto','NOT LIKE','%BANREGIO%')
                                ->where('concepto','NOT LIKE','%CAJA POP MEX%')->where('concepto','NOT LIKE','%COMPARTAMOS%')
                                ->where('concepto','NOT LIKE','%HSBC%')->where('concepto','NOT LIKE','%INBURSA%')
                                ->where('concepto','NOT LIKE','%SANTANDER%')->where('concepto','NOT LIKE','%SCOTIABANK%')
                                ->where('fecha',$registro->date)
                                ->where(function($query) use ($invoice, $auto){
                                    $query->where('concepto','like','%'.$invoice.'%')
                                        ->orWhere('concepto','like','%'.$auto.'%');
                                })
                                ->where('abono','like','%'.$registro->total.'%')
                                ->where('occupied', 0)
                                ->first();
                }
            }
        }
        // BANCO AZTECA
        if($registro->type === 'BANCO AZTECA'){
            // $folio = Folio::where('fecha',$registro->date)
            //     ->where('abono','like','%'.$registro->total.'%')
            //     ->where('occupied', 0)
            //     ->where('concepto','like','%DEPOSITO DE EFECTIVO/'.$registro->auto.'%')
            //     // ->where(function($query){
            //     //     $query->where('concepto','like','%DEPOSITO EN EFECTIVO/%')
            //     //             ->orWhere('concepto','like','%DEPOSITO DE EFECTIVO/%');
            //     // })
            //     ->first();
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
        $students = Student::where('check', 'rejected')
                    ->where('created_at', 'like', '%'.$request->number_rejected.'%')
                    ->with('registros')->orderBy('name', 'desc')
                    ->get();
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

                    $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente.';

                    array_push($estudiantes, ['student' => $student, 'message' => $message]);
                } else {
                    $count_process = Registro::where('student_id', $student->id)
                            ->where('status', 'process')->count();
                    if($count_process === 0){
                        $student->update(['check' => 'rejected']);
                        $message = 'Tu pre-registro no pudo ser aceptado, te pedimos verifiques tus datos y vuelvas a registrarte ingresando correctamente tus datos.';
                        // Puedes consultar tus datos ingresando al siguiente link (Solo estará disponible por 3 días).
                        if($student->validate == 'NO ENVIADO' && $student->school_id != 76){
                            array_push($estudiantes, ['student' => $student, 'message' => $message]);
                        }
                    }
                }

            }
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }

        
        foreach ($estudiantes as $estudiante) {
            $s = Student::whereId($estudiante['student']['id'])->first();
            if($s->check !== 'process'){
                if($student->school_id != 1){ // NO ENVIAR CORREO A LOS ALUMNOS DE CAMPECHE
                    Mail::to($s->email)->send(new PreRegister($estudiante['message'], $s));
                    $s->update(['validate' => 'ENVIADO']);
                }
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

        $message = 'Tu registro se ha completado. Los datos que ingresaste en tu pre-registro han sido verificados correctamente.';

        Mail::to($student->email)->send(new PreRegister($message, $student));
        $student->update(['validate' => 'ENVIADO']);

        $hoy = Carbon::now()->format('Y-m-d');
        $students = Student::where('check', 'accepted')
                    ->where('created_at', 'like', '%'.$hoy.'%')
                    ->with('school')->orderBy('created_at', 'desc')->get();

        return response()->json($students);
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
