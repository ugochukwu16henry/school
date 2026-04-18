<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasSchoolScope;

    protected $fillable = [
        'title', 
        'start', 
        'end',
        'session_id',
        'school_id',
    ];
}
