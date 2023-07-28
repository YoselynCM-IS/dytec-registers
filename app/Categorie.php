<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Student;

class Categorie extends Model
{
    protected $fillable = [
        'id', 'categorie', 'school_id', 'creado_por', 'archivado'
    ];

    public function students(){
        return $this->hasMany(Student::class);
    }
}
