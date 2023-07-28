<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Registro;
use App\Student;

class ArchivoBExport implements FromView
{
    protected $inicio;
    protected $final;

    public function __construct($inicio, $final)
    {
        $this->inicio = $inicio;
        $this->final = $final;
    }

    public function view(): View
    {
        return view('download.archivo-banxico', [
            'registros' => $this->get_registros()
        ]);
    }
    
    public function get_registros(){
        $ids_rs = Registro::select('student_id')->where('clave', '!=', '')
                    ->whereBetween('date',[$this->inicio, $this->final])        
                    ->where('bank', '!=', 'BANCOMER')->get();
        $ids_ss = Student::select('id')->whereIn('id', $ids_rs)
                    ->where('check', '!=', 'accepted')->get();
        $rs = Registro::whereIn('student_id', $ids_ss)->get();
        
        $a = array();
        foreach ($rs as $r) {
            $clave_emisor = '00000';

            if($r->bank == 'BANAMEX') $clave_emisor = '40002';
            if($r->bank == 'BANCO AZTECA') $clave_emisor = '40127';
            if($r->bank == 'BANCOPPEL') $clave_emisor = '40137';
            if($r->bank == 'BANBAJIO') $clave_emisor = '40030';
            if($r->bank == 'BANORTE') $clave_emisor = '40072';
            if($r->bank == 'BANREGIO') $clave_emisor = '40058';
            if($r->bank == 'CAJA POPULAR MEXICANA') $clave_emisor = '90677';
            if($r->bank == 'COMPARTAMOS') $clave_emisor = '40130';
            if($r->bank == 'HSBC') $clave_emisor = '40021';
            if($r->bank == 'INBURSA') $clave_emisor = '40036';
            if($r->bank == 'SANTANDER') $clave_emisor = '40014';
            if($r->bank == 'SCOTIABANK') $clave_emisor = '40044';

            $data = [
                'date' => $r->date, 'clave' => $r->clave, 'clave_emisor' => $clave_emisor, 'total' => $r->total
            ];
            array_push($a, $data);
        }

        $registros = collect($a);

        return $registros;
    }
}
