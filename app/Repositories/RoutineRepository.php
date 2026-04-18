<?php

namespace App\Repositories;

use App\Models\Routine;
use App\Interfaces\RoutineInterface;

class RoutineRepository implements RoutineInterface {
    public function saveRoutine($request)
    {
        try{
            Routine::create([
                'start'         => $request['start'],
                'end'           => $request['end'],
                'weekday'       => $request['weekday'],
                'session_id'    => $request['session_id'],
                'class_id'      => $request['class_id'],
                'section_id'    => $request['section_id'],
                'course_id'     => $request['course_id'],
                'school_id'     => $request['school_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to save routine. '.$e->getMessage());
        }
    }

    public function getAll($class_id, $section_id, $session_id, $schoolId = null) {
        return Routine::with('course')
                ->where('session_id', $session_id)
                ->where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->when($schoolId !== null, function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
                ->get();
    }
}