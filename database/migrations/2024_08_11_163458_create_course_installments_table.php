<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseInstallmentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_installments', function (Blueprint $table) {
         $table->id();
         $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); // Link to the course
         $table->foreignId('course_schedule_id')
            ->constrained('course_schedules')
            ->onDelete('cascade')
            ->after('course_id');
         $table->foreignId('student_id')->constrained('students')->onDelete('cascade')->after('id');;
         $table->date('start_date'); // Start date of the installment period
         $table->date('end_date'); // End date of the installment period
         $table->decimal('installment_amount', 8, 2); // Amount for each installment
         $table->foreignId('number_of_installments')->nullable();
         $table->integer('completed_installments')->nullable()->default(0);
         $table->date('next_reminder_date')->nullable();
         $table->date('due_date')->nullable();
         $table->decimal('paid_amount', 10, 2);
         $table->string('next_reminder_days', 20);
         $table->string('due_days', 20)->nullable();
         $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
         $table->enum('status', ['active', 'inactive'])->nullable()->default('active');
         $table->timestamp('created_at')->useCurrent()->nullable(); // Default value
         $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable(); // Default value with a
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('course_installments');
   }
}
