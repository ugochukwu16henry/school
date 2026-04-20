<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClaimFieldsToStudentParentInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_parent_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('student_parent_infos', 'parent_email')) {
                $table->string('parent_email')->nullable()->after('mother_phone');
            }

            if (!Schema::hasColumn('student_parent_infos', 'parent_user_id')) {
                $table->unsignedBigInteger('parent_user_id')->nullable()->after('parent_email');
                $table->index('parent_user_id', 'spi_parent_user_id_idx');
            }

            if (!Schema::hasColumn('student_parent_infos', 'claim_code')) {
                $table->string('claim_code', 32)->nullable()->after('parent_user_id');
                $table->unique('claim_code', 'spi_claim_code_unique');
            }

            if (!Schema::hasColumn('student_parent_infos', 'claim_code_generated_at')) {
                $table->timestamp('claim_code_generated_at')->nullable()->after('claim_code');
            }

            if (!Schema::hasColumn('student_parent_infos', 'claim_code_claimed_at')) {
                $table->timestamp('claim_code_claimed_at')->nullable()->after('claim_code_generated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_parent_infos', function (Blueprint $table) {
            if (Schema::hasColumn('student_parent_infos', 'claim_code_claimed_at')) {
                $table->dropColumn('claim_code_claimed_at');
            }

            if (Schema::hasColumn('student_parent_infos', 'claim_code_generated_at')) {
                $table->dropColumn('claim_code_generated_at');
            }

            if (Schema::hasColumn('student_parent_infos', 'claim_code')) {
                $table->dropUnique('spi_claim_code_unique');
                $table->dropColumn('claim_code');
            }

            if (Schema::hasColumn('student_parent_infos', 'parent_user_id')) {
                $table->dropIndex('spi_parent_user_id_idx');
                $table->dropColumn('parent_user_id');
            }

            if (Schema::hasColumn('student_parent_infos', 'parent_email')) {
                $table->dropColumn('parent_email');
            }
        });
    }
}
