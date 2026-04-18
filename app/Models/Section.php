<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;

class Section extends Model
{
    use HasFactory, HasSchoolScope;

    protected $fillable = ['section_name', 'room_no', 'class_id', 'session_id', 'school_id'];

    public function schoolClass() {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
