<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Student;

class Comprobante extends Model
{
    protected $fillable = [
        'student_id', 'name', 'size', 'extension', 'public_url'
    ];

    public function student(){
        return $this->belongsTo(Student::class);
    }
}
