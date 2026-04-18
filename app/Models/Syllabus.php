<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    use HasFactory, HasSchoolScope;

    protected $fillable = [
        'syllabus_name',
        'syllabus_file_path',
        'class_id',
        'course_id',
        'session_id',
        'school_id',
    ];
}
