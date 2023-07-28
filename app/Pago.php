<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\School;

class Pago extends Model
{
    protected $fillable = [
        'id', 'school_id', 'total_comision', 'total_libros', 'comision_libro',
        'name', 'size', 'extension', 'public_url'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }
}
