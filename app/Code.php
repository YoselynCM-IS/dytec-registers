<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    protected $fillable = [
        'student_id', 'editorial', 'code1', 'code2', 'code3'
    ];
}
