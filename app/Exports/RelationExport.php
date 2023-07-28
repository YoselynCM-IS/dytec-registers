<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\School;

class RelationExport implements FromView
{
    public function view(): View
    {
        $schools = School::with('books')->orderBy('name', 'asc')->get();
        return view('download.relation-schools', [
            'schools' => $schools
        ]);
    }
}
