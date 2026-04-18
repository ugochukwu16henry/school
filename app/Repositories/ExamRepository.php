<?php

namespace App\Repositories;

use App\Models\Exam;
use App\Models\Semester;
use App\Models\SchoolClass;
use App\Interfaces\ExamInterface;

class ExamRepository implements ExamInterface {
    public function create($request) {
        try {
            Exam::create($request);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create exam. '.$e->getMessage());
        }
    }

    public function delete($id, $schoolId = null) {
        try {
            $query = Exam::where('id', $id);

            if ($schoolId !== null) {
                $query->where('school_id', $schoolId);
            }

            $query->delete();
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete exam. '.$e->getMessage());
        }
    }

    public function getAll($session_id, $semester_id, $class_id, $schoolId = null)
    {
        if($semester_id == 0 || $class_id == 0) {
            $semester = Semester::where('session_id', $session_id)
                ->when($schoolId !== null, function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
                ->first();

            $schoolClass = SchoolClass::where('session_id', $session_id)
                ->when($schoolId !== null, function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
                ->first();

            if (!$semester || !$schoolClass) {
                return collect();
            }

            $semester_id = $semester->id;
            $class_id = $schoolClass->id;
        }

        return Exam::with('course')
                    ->where('session_id', $session_id)
                    ->where('semester_id', $semester_id)
                    ->where('class_id', $class_id)
                    ->when($schoolId !== null, function ($query) use ($schoolId) {
                        return $query->where('school_id', $schoolId);
                    })
                    ->get();
    }
}