<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory, HasSchoolScope;

    protected $fillable = ['semester_name', 'start_date', 'end_date', 'session_id', 'school_id'];
}
