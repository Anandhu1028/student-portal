<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursePaymentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_payments', function (Blueprint $table) {
         $table->id();
         $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
         $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');
         $table->foreignId('university_id')->nullable()->constrained('universities')->onDelete('cascade');
         $table->foreignId('course_schedule_id')->nullable()->constrained('course_schedules')->onDelete('cascade');
         $table->date('admission_date')->nullable(); // Example field for status
         $table->decimal('amount', 8, 4);
         $table->double('tax_rate', 10, 4)->nullable();
         $table->decimal('discount', 8, 4)->nullable()->default(0);
         $table->enum('payment_status', ['pending', 'completed']);
         $table->string('payment_option')->nullable();
         $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
         $table->date('next_payment_date')->nullable(); // Example field for status
         $table->enum('status', ['active', 'inactive', 'pending'])->nullable()->default('active');
         $table->string('student_track_id')->unique();
         $table->foreignId('created_by')->nullable()->constrained('users')->nullable()->onDelete('set null');
         $table->foreignId('branch_id')->nullable()->constrained('branches')->nullable()->onDelete('set null');
         $table->string('university_loginid')->nullable();
         $table->string('university_loginpass')->nullable();

         $table->foreignId('admitted_for')->nullable()->constrained('users')->onDelete('set null');
         $table->foreignId('admitted_by')->nullable()->constrained('users')->onDelete('set null');
         $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
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
      Schema::dropIfExists('course_payments');
   }
}
