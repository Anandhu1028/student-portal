<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('tasks', function (Blueprint $table) {
         $table->id();
         $table->string('title');
         $table->text('description')->nullable();
         $table->string('task_type')->nullable(); // general, approval, reminder
         $table->foreignId('task_type_id')
            ->nullable()
            ->constrained('task_types')
            ->nullOnDelete();
         // Workflow
         $table->foreignId('task_status_id')
            ->nullable()
            ->constrained('task_statuses')
            ->nullOnDelete();

         $table->foreignId('task_priority_id')
            ->nullable()
            ->constrained('task_priorities')
            ->nullOnDelete();

         // 🔗 NULLABLE FK TO YOUR TABLES
         $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
         $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
         $table->foreignId('course_payment_id')->nullable()->constrained('course_payments')->nullOnDelete();
         $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete();

         // Sub-tasks
         // $table->foreignId('parent_id')->nullable()->constrained('tasks')->cascadeOnDelete();
         $table->foreignId('task_owner_id')->nullable()->constrained('users')->nullOnDelete();
         // Ownership


         // Time control
         $table->timestamp('start_at')->nullable();
         $table->timestamp('due_at')->nullable();
         $table->timestamp('completed_at')->nullable();

         // SLA
         $table->integer('sla_minutes')->nullable();
         $table->timestamp('escalate_at')->nullable();

         $table->enum('visibility', ['private', 'team', 'public', 'system'])->default('team');

         $table->softDeletes();
         $table->timestamps();
         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

         $table->index(['student_id', 'course_payment_id']);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('tasks');
   }
}
