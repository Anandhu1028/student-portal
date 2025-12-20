<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchChangeLogsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('batch_change_logs', function (Blueprint $table) {
         $table->id();

         // Student who changed batch
         $table->foreignId('student_id')
            ->constrained('students')
            ->onDelete('cascade');

         // Old batch (from course_schedules)
         $table->foreignId('old_batch_id')
            ->nullable()
            ->constrained('course_schedules')
            ->onDelete('set null');

         // Old track id used before change
         $table->string('old_track_id')->nullable();

         // New batch after change
         $table->foreignId('new_batch_id')
            ->nullable()
            ->constrained('course_schedules')
            ->onDelete('set null');

         // Why the change happened
         $table->text('reason')->nullable();

         // Date the change happened
         $table->timestamp('changed_date')->nullable()->useCurrent();

         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('batch_change_logs');
   }
}
