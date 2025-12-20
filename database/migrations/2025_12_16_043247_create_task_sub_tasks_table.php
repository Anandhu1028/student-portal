<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskSubTasksTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('task_sub_tasks', function (Blueprint $table) {
         $table->id();
         $table->foreignId('task_id')
            ->constrained('tasks')
            ->cascadeOnDelete();

         $table->string('title');
         $table->text('description')->nullable();

         $table->foreignId('task_status_id')
            ->nullable()
            ->constrained('task_statuses')
            ->nullOnDelete();

         $table->foreignId('task_priority_id')
            ->nullable()
            ->constrained('task_priorities')
            ->nullOnDelete();

         // Sub-task owner
         $table->foreignId('task_owner_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

         // Scheduling
         $table->timestamp('start_at')->nullable();
         $table->timestamp('due_at')->nullable();
         $table->timestamp('completed_at')->nullable();

         $table->softDeletes();
         $table->timestamps();

         $table->index(['task_id', 'task_status_id']);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('task_sub_tasks');
   }
}
