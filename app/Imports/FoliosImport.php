<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class FoliosImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {

    }

    // public function model(array $row)
    // {
    //     // $search_folio = Folio::where([
    //     //     'fecha' => $row[0], 'concepto' => $row[1], 
    //     //     'abono' => $row[2], 'saldo' => $row[3]
    //     // ])->first();

    //     // $folio = $search_folio;
    //     // if($search_folio === null){
    //     //     $folio = Folio::create([
    //     //         'fecha' => $row[0], 'concepto' => $row[1], 
    //     //         'abono' => $row[2], 'saldo' => $row[3]
    //     //     ]);
    //     // }

    //     // return $folio;
    // }
}
