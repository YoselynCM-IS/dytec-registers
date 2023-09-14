<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FoliosImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Folio;

class FolioController extends Controller
{
    public function index(){
        return view('depositos.lista');
    }

    // MOSTRAR TODOS LOS FOLIOS
    public function by_month(){
        $mes_actual = Carbon::now()->format('Y-m');
        $folios = Folio::where('fecha', 'like', '%'.$mes_actual.'%')->orderBy('created_at', 'desc')->paginate(50);
        return response()->json($folios);
    }

    // BUSCAR FOLIO
    public function get_folio(Request $request){
        // return response()->json($request);
        if($request->auto !== ''){
            $folio = Folio::where('fecha',$request->date)
                    ->where('abono','like','%'.$request->total.'%')
                    ->where('concepto','like','%'. Str::of($request->auto)->upper().'%')
                    ->where('concepto','like','%'.$request->folio.'%')->first();
        } else {
            $folio = Folio::where('fecha',$request->date)
                    ->where('abono','like','%'.$request->total.'%')
                    ->where('concepto','like','%'.$request->folio.'%')->first();
        }

        if($folio === null) return response()->json($data = 'NO EXISTE');
        else return response()->json($folio);
    }

    public function search_folios(Request $request){
        if($request->bank === 'BANCOMER'){
            $folios = Folio::where('fecha',$request->fecha)
                ->where('abono','like','%'.$request->abono.'%')
                ->where(function($query){
                    $query->where('concepto', 'like', '%DEPOSITO EFECTIVO PRACTIC/%')
                    ->orWhere('concepto', 'like', '%DEPOSITO EN EFECTIVO/%')
                    ->orWhere('concepto', 'like', '%DEPOSITO POR CORRECCION/%')
                    ->orWhere('concepto', 'like', '%PAGO CUENTA DE TERCERO/%');
                })
                ->where('occupied', 0)
                ->get();
        }
        if($request->bank === 'OTRO'){
            $folios = Folio::where('concepto', 'NOT LIKE', '%DEPOSITO EFECTIVO PRACTIC/%')
                ->where('concepto', 'NOT LIKE', '%DEPOSITO EN EFECTIVO/%')
                ->where('concepto', 'NOT LIKE', '%DEPOSITO POR CORRECCION/%')
                ->where('concepto', 'NOT LIKE', '%PAGO CUENTA DE TERCERO/%')
                ->where('fecha',$request->fecha)
                ->where('abono','like','%'.$request->abono.'%')
                ->where('occupied', 0)
                ->get();
        }
        return response()->json($folios);
    }

    public function store(Request $request){
        $array = Excel::toArray(new FoliosImport, request()->file('file'));
        \DB::beginTransaction();
        try {
            $countF = 0;
            $lista = collect($array[0]);
            $lista->map(function($row) use(&$countF){
                $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]);
                $search_folio = Folio::where([
                    'fecha' => $fecha, 'concepto' => $row[1], 
                    'abono' => $row[2], 'saldo' => $row[3]
                ])->first();

                if($search_folio === null){
                    Folio::create([
                        'fecha' => $fecha, 'concepto' => $row[1], 
                        'abono' => $row[2], 'saldo' => $row[3]
                    ]);
                    $countF++;
                }
            });
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json(['guardados' => $countF, 'total' => count($lista)]);
    }

    public function show(Request $request){
        $folio = Folio::whereId($request->folio_id)->first();
        return response()->json($folio);
    }

    public function by_date(Request $request){
        $folios = Folio::where('fecha', $request->fecha)
            ->orderBy('created_at', 'desc')->paginate(50);
        return response()->json($folios);
    }

    public function by_date_abono(Request $request){
        $folios = Folio::where('fecha', $request->fecha)
                        ->where('abono', $request->abono)
                        ->orderBy('created_at', 'desc')->paginate(50);
        return response()->json($folios);
    }

    public function by_referencia(Request $request) {
        $folios = Folio::where('fecha', $request->fecha)
                        ->where('abono', $request->abono)
                        ->where('concepto', 'like', '%'.$request->referencia.'%')
                        ->orderBy('created_at', 'desc')->paginate(50);
        return response()->json($folios);
    }

    public function by_bank(Request $request){
        if($request->bank !== 'BANCOMER' && $request->bank !== 'OTRO'){
            $folios = Folio::where('concepto','like','%'.$request->bank.'%')->paginate(50);
        } 
        if($request->bank === 'BANCOMER'){
            $folios = Folio::where('concepto', 'like', '%DEPOSITO EFECTIVO PRACTIC%')
            ->orWhere('concepto', 'like', '%DEPOSITO EN EFECTIVO/%')
            ->orWhere('concepto', 'like', '%DEPOSITO POR CORRECCION/%')
            ->orWhere('concepto', 'like', '%PAGO CUENTA DE TERCERO%')
            ->paginate(50);
        }
        if($request->bank === 'OTRO'){
            $folios = Folio::where('concepto','NOT LIKE','%DEPOSITO EFECTIVO PRACTIC%')
            ->where('concepto','NOT LIKE','%DEPOSITO EN EFECTIVO/%')
            ->where('concepto','NOT LIKE','%DEPOSITO POR CORRECCION/%')
            ->where('concepto','NOT LIKE','%PAGO CUENTA DE TERCERO%')
            ->where('concepto','NOT LIKE','%BANAMEX%')->where('concepto','NOT LIKE','%AZTECA%')
            ->where('concepto','NOT LIKE','%BANCOPPEL%')->where('concepto','NOT LIKE','%BAJIO%')
            ->where('concepto','NOT LIKE','%BANORTE%')->where('concepto','NOT LIKE','%BANREGIO%')
            ->where('concepto','NOT LIKE','%CAJA POP MEX%')->where('concepto','NOT LIKE','%COMPARTAMOS%')
            ->where('concepto','NOT LIKE','%HSBC%')->where('concepto','NOT LIKE','%INBURSA%')
            ->where('concepto','NOT LIKE','%SANTANDER%')->where('concepto','NOT LIKE','%SCOTIABANK%')
            ->paginate(50);
        }
        return response()->json($folios);
    }

    public function marcar_ocupado(Request $request){
        \DB::beginTransaction();
        try {
            $folio = Folio::whereId($request->id)->first();
            $folio->update([
                'occupied' => 1,
                'marcado_por' => auth()->user()->name
            ]);
            \DB::commit();
        }  catch (Exception $e) {
            \DB::rollBack();
        }
        return response()->json($folio);
    }
}
