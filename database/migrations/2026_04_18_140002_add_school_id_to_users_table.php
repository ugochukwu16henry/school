<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSchoolIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->after('role');
        });

        $defaultSchoolId = DB::table('schools')->where('slug', 'default-school')->value('id');

        if (!$defaultSchoolId) {
            $now = now();
            $defaultSchoolId = DB::table('schools')->insertGetId([
                'name' => 'Default School',
                'slug' => 'default-school',
                'status' => 'active',
                'plan' => 'trial',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('users')
            ->whereNull('school_id')
            ->where('role', '!=', 'super_admin')
            ->update(['school_id' => $defaultSchoolId]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
}
