<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Student;
use App\Book;

class School extends Model
{
    protected $fillable = [ 'id', 'name' ];

    public function students(){
        return $this->hasMany(Student::class);
    }

    public function books(){
        return $this->belongsToMany(Book::class)->withPivot('price');
    }
}
