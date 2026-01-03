<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentCourseBatchTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('student_course_batch_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('course_batch_type_id')
                ->constrained('course_batch_types')
                ->cascadeOnDelete();

            $table->timestamps();

            //  IMPORTANT: SHORT, MANUAL INDEX NAME
            $table->unique(
                ['student_id', 'course_batch_type_id'],
                'uq_student_batch'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_course_batch_types');
    }
}
