<?php

namespace App\Http\Controllers;

use App\Exports\MovimientosExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Registro;
use App\Student;
use Excel;

class MovimientoController extends Controller
{
    public function by_month(Request $request){
        $anio = Carbon::now()->format('Y');
        $date = $anio.'-'.$request->month;

        $registros  = Registro::select('student_id')
                        ->where('date', 'like', '%'.$date.'%')
                        ->groupBy('student_id')->get();
        
        if($request->state == 'accepted'){
            $movimientos = Student::whereIn('id',$registros)
                    ->where('check', $request->state)
                    ->withTrashed()->with('school', 'registros.folio')
                    ->orderBy('created_at', 'desc')->paginate(50);
            $count_movimientos = Student::whereIn('id',$registros)
                    ->where('check', $request->state)
                    ->withTrashed()->count();
        } else {
            $movimientos = Student::whereIn('id',$registros)
                    ->where('check', $request->state)
                    ->with('school', 'registros.folio')
                    ->orderBy('created_at', 'desc')->paginate(50);
            $count_movimientos = Student::whereIn('id',$registros)
                    ->where('check', $request->state)->count();
        }
        
        $data = [
            'movimientos' => $movimientos,
            'count_movimientos' => $count_movimientos
        ];
        return response()->json($data);
    }

    public function down_by_month($month, $status){
        $anio = Carbon::now()->format('Y');
        $date = $anio.'-'.$month;
        return Excel::download(new MovimientosExport($date, $status), 'movimientos.xlsx');
    }
}
