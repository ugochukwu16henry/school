<?php

namespace App\Repositories;

use App\Models\StudentParentInfo;

class StudentParentInfoRepository {
    public function store($request, $student_id) {
        try {
            StudentParentInfo::create([
                'student_id'    => $student_id,
                'father_name'   => $request['father_name'],
                'father_phone'  => $request['father_phone'],
                'mother_name'   => $request['mother_name'],
                'mother_phone'  => $request['mother_phone'],
                'parent_address'=> $request['parent_address'],
                'school_id'     => $request['school_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Student Parent information. '.$e->getMessage());
        }
    }

    public function getParentInfo($student_id, $schoolId = null) {
        return StudentParentInfo::where('student_id', $student_id)
                ->when($schoolId !== null, function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
                ->first();
    }

    public function update($request, $student_id, $schoolId = null) {
        try {
            StudentParentInfo::where('student_id', $student_id)
                ->when($schoolId !== null, function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
                ->update([
                'father_name'   => $request['father_name'],
                'father_phone'  => $request['father_phone'],
                'mother_name'   => $request['mother_name'],
                'mother_phone'  => $request['mother_phone'],
                'parent_address'=> $request['parent_address'],
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to update Student Parent information. '.$e->getMessage());
        }
    }
}