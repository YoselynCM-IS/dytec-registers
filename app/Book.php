<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\School;

class Book extends Model
{
    protected $fillable = [ 'id', 'name', 'editorial', 'price' ];
    
    // public function schools(){
    //     return $this->belongsToMany(School::class)->withPivot('price');
    // }
}
