<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSchoolIdToAcademicTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = $this->tables();

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $schemaTable) {
                    $schemaTable->unsignedBigInteger('school_id')->nullable()->after('id');
                });
            }
        }

        $defaultSchoolId = DB::table('schools')->where('slug', 'default-school')->value('id');

        if (!$defaultSchoolId) {
            $defaultSchoolId = DB::table('schools')->value('id');
        }

        if ($defaultSchoolId) {
            foreach ($tables as $table) {
                DB::table($table)->whereNull('school_id')->update(['school_id' => $defaultSchoolId]);
            }
        }

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $schemaTable) use ($table) {
                $schemaTable->foreign('school_id', $table . '_school_id_foreign')
                    ->references('id')
                    ->on('schools')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables() as $table) {
            if (Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $schemaTable) use ($table) {
                    $schemaTable->dropForeign($table . '_school_id_foreign');
                    $schemaTable->dropColumn('school_id');
                });
            }
        }
    }

    private function tables()
    {
        return [
            'school_sessions',
            'semesters',
            'school_classes',
            'sections',
            'courses',
            'academic_settings',
            'promotions',
            'exam_rules',
            'grade_rules',
            'marks',
            'exams',
            'student_parent_infos',
            'student_academic_infos',
            'attendances',
            'notices',
            'events',
            'syllabi',
            'routines',
            'assigned_teachers',
            'grading_systems',
            'final_marks',
            'assignments',
        ];
    }
}
