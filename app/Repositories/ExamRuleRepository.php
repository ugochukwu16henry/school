<?php

namespace App\Repositories;

use App\Interfaces\ExamRuleInterface;
use App\Models\ExamRule;

class ExamRuleRepository implements ExamRuleInterface {
    public function create($request) {
        try {
            ExamRule::create($request);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create exam rule. '.$e->getMessage());
        }
    }

    public function update($request, $schoolId = null) {
        try {
            ExamRule::where('id', $request->exam_rule_id)
                ->when($schoolId !== null, function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
                ->update([
                'total_marks'   => $request->total_marks,
                'pass_marks'    => $request->pass_marks,
                'marks_distribution_note'   => $request->marks_distribution_note
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to update exam rule. '.$e->getMessage());
        }
    }

    public function getAll($session_id, $exam_id, $schoolId = null) {
        return ExamRule::where('session_id', $session_id)
                        ->when($schoolId !== null, function ($query) use ($schoolId) {
                            return $query->where('school_id', $schoolId);
                        })
                        ->where('exam_id', $exam_id)
                        ->get();
    }

    public function getById($exam_rule_id, $schoolId = null) {
        return ExamRule::where('id', $exam_rule_id)
                        ->when($schoolId !== null, function ($query) use ($schoolId) {
                            return $query->where('school_id', $schoolId);
                        })
                        ->first();
    }
}