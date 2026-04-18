<?php

namespace App\Repositories;

use App\Models\GradeRule;

class GradeRuleRepository {
    public function store($request) {
        try {
            GradeRule::create($request);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create grading system rule. '.$e->getMessage());
        }
    }

    public function delete($id, $schoolId = null) {
        try {
            $query = GradeRule::where('id', $id);

            if ($schoolId !== null) {
                $query->where('school_id', $schoolId);
            }

            $query->delete();
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete grading system rule. '.$e->getMessage());
        }
    }

    public function getAll($session_id, $grading_system_id, $schoolId = null) {
        return GradeRule::with('gradingSystem')->where('grading_system_id', $grading_system_id)
                    ->where('session_id', $session_id)
                    ->when($schoolId !== null, function ($query) use ($schoolId) {
                        return $query->where('school_id', $schoolId);
                    })
                    ->get();
    }
}