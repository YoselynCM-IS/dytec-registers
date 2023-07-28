<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Comprobante;
use App\Registro;
use App\School;

class Student extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'categorie_id', 'school_id', 'name', 'email', 'telephone', 
        'book', 'quantity', 'price', 'total', 'check', 
        'delivery', 'date_delivery', 'user_delivery',
        'codes', 'date_codes', 'user_codes', 'send_codes',
        'validate', 'teacher', 'numorden',
        'reviewed', 'date_reviewed', 'numcuenta'
    ];

    public function school(){
        return $this->belongsTo(School::class);
    }

    public function registros(){
        return $this->hasMany(Registro::class);
    }

    public function comprobantes(){
        return $this->hasMany(Comprobante::class);
    }
}
