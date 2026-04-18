<?php

namespace App\Repositories;

use App\Models\Semester;
use App\Models\AssignedTeacher;
use App\Interfaces\AssignedTeacherInterface;

class AssignedTeacherRepository implements AssignedTeacherInterface {

    public function assign($request) {
        try {
            AssignedTeacher::create($request);
        } catch (\Exception $e) {
            throw new \Exception('Failed to assign teacher. '.$e->getMessage());
        }
    }

    public function getTeacherCourses($session_id, $teacher_id, $semester_id) {
        if($semester_id == 0) {
            $semester = Semester::where('session_id', $session_id)
                ->where('school_id', auth()->user()->school_id)
                ->first();

            if (!$semester) {
                return collect();
            }

            $semester_id = $semester->id;
        }
        return AssignedTeacher::with(['course', 'schoolClass', 'section'])->where('session_id', $session_id)
                        ->where('teacher_id', $teacher_id)
                        ->where('semester_id', $semester_id)
                        ->where('school_id', auth()->user()->school_id)
                        ->get(); 
    }

    public function getAssignedTeacher($session_id, $semester_id, $class_id, $section_id, $course_id) {
        if($semester_id == 0) {
            $semester = Semester::where('session_id', $session_id)
                ->where('school_id', auth()->user()->school_id)
                ->first();

            if (!$semester) {
                return null;
            }

            $semester_id = $semester->id;
        }
        return AssignedTeacher::where('session_id', $session_id)
                        ->where('semester_id', $semester_id)
                        ->where('class_id', $class_id)
                        ->where('section_id', $section_id)
                        ->where('course_id', $course_id)
                        ->where('school_id', auth()->user()->school_id)
                        ->first(); 
    }
}