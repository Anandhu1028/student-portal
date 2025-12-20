<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskActivitiesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('task_activities', function (Blueprint $table) {
         $table->id();
         $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
         $table->foreignId('sub_task_id')
            ->nullable() // REQUIRED for SET NULL
            ->constrained('task_sub_tasks')
            ->nullOnDelete();
         $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
         $table->foreignId('task_owner_id')->nullable()->constrained('users')->nullOnDelete();
         $table->foreignId('task_activity_type_id')->nullable()->constrained('task_activity_types')->nullOnDelete();
         $table->text('message')->nullable();
         $table->timestamp('due_at')->nullable();
         $table->foreignId('from_status_id')->nullable()->constrained('task_statuses')->nullOnDelete();
         $table->foreignId('to_status_id')->nullable()->constrained('task_statuses')->nullOnDelete();
         $table->json('target_user_ids')->nullable();
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
      Schema::dropIfExists('task_activities');
   }
}
