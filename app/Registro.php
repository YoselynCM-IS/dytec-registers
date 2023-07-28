<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Student;
use App\Folio;

class Registro extends Model
{
    protected $fillable = [
        'student_id', 'folio_id', 'type', 'invoice', 'auto', 'clave',
        'total', 'date', 'plaza', 'bank', 'status', 'cajero'
    ];

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function folio(){
        return $this->belongsTo(Folio::class);
    }
}
