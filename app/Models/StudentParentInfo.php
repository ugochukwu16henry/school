<?php

namespace App\Models;

use App\Models\Concerns\HasSchoolScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentParentInfo extends Model
{
    use HasFactory, HasSchoolScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'father_name',
        'father_phone',
        'mother_name',
        'mother_phone',
        'parent_email',
        'parent_user_id',
        'claim_code',
        'claim_code_generated_at',
        'claim_code_claimed_at',
        'parent_address',
        'school_id',
    ];

    protected $casts = [
        'claim_code_generated_at' => 'datetime',
        'claim_code_claimed_at' => 'datetime',
    ];

    /**
     * Get the sections for the blog post.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
}
