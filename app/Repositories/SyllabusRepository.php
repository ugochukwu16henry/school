<?php

namespace App\Repositories;

use App\Models\Syllabus;
use Illuminate\Support\Facades\Storage;

class SyllabusRepository {
    public function store($request) {
        // Automatically generate a unique ID for filename...
        $path = Storage::disk('public')->put('syllabi', $request['file']);
        try {
            Syllabus::create([
                'syllabus_name'           => $request['syllabus_name'],
                'syllabus_file_path'      => $path,
                'class_id'                => $request['class_id'],
                'course_id'               => $request['course_id'],
                'session_id'              => $request['session_id'],
                'school_id'               => $request['school_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create syllabus. '.$e->getMessage());
        }
    }

    public function getByClass($class_id, $schoolId = null) {
        return Syllabus::where('class_id', $class_id)
            ->when($schoolId !== null, function ($query) use ($schoolId) {
                return $query->where('school_id', $schoolId);
            })
            ->get();
    }

    public function getByCourse($course_id, $schoolId = null) {
        return Syllabus::where('course_id', $course_id)
            ->when($schoolId !== null, function ($query) use ($schoolId) {
                return $query->where('school_id', $schoolId);
            })
            ->get();
    }
}