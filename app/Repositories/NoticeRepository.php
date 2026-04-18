<?php

namespace App\Repositories;

use App\Models\Notice;

class NoticeRepository {
    public function store($request) {
        try {
            Notice::create([
                'notice'        => $request['notice'],
                'session_id'    => $request['session_id'],
                'school_id'     => $request['school_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to save Notice. '.$e->getMessage());
        }
    }

    public function getAll($session_id, $schoolId = null) {
        return Notice::where('session_id', $session_id)
                    ->when($schoolId !== null, function ($query) use ($schoolId) {
                        return $query->where('school_id', $schoolId);
                    })
                    ->orderBy('id', 'desc')
                    ->simplePaginate(3);
    }
}