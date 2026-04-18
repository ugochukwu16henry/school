<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamRule extends Model
{
    use HasFactory, HasSchoolScope;

    protected $fillable = [
        'total_marks',
        'pass_marks',
        'marks_distribution_note',
        'exam_id',
        'session_id',
        'school_id',
    ];
}
