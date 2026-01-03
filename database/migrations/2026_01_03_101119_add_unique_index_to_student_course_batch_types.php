<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueIndexToStudentCourseBatchTypes extends Migration
{
    public function up()
    {
        // Ensure table exists
        if (!Schema::hasTable('student_course_batch_types')) {
            return;
        }

        // Check if index already exists
        $exists = DB::select("
            SHOW INDEX 
            FROM student_course_batch_types 
            WHERE Key_name = 'uq_student_batch'
        ");

        // Only add index if it DOES NOT exist
        if (empty($exists)) {
            Schema::table('student_course_batch_types', function (Blueprint $table) {
                $table->unique(
                    ['student_id', 'course_batch_type_id'],
                    'uq_student_batch'
                );
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('student_course_batch_types')) {
            return;
        }

        // Drop index ONLY if it exists
        $exists = DB::select("
            SHOW INDEX 
            FROM student_course_batch_types 
            WHERE Key_name = 'uq_student_batch'
        ");

        if (!empty($exists)) {
            Schema::table('student_course_batch_types', function (Blueprint $table) {
                $table->dropUnique('uq_student_batch');
            });
        }
    }
}
