<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory, HasSchoolScope;

    protected $fillable = ['course_name', 'course_type', 'class_id', 'semester_id', 'session_id', 'school_id'];
}
