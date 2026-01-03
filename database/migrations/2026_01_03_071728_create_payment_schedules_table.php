<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
         $table->id();

         $table->foreignId('student_id')
            ->constrained('students')
            ->onDelete('cascade');


         $table->foreignId('student_interaction_id')
            ->nullable()
            ->constrained('student_interactions')
            ->onDelete('set null');

         $table->foreignId('university_id')
            ->nullable()
            ->constrained('universities')
            ->onDelete('set null');

         $table->foreignId('course_id')
            ->nullable()
            ->constrained('courses')
            ->onDelete('set null');


         $table->foreignId('course_payment_id')
            ->nullable()
            ->constrained('course_payments')
            ->onDelete('set null');

         // Task manager linkage
         $table->foreignId('task_id')
            ->nullable()
            ->constrained('tasks')
            ->onDelete('set null');

         $table->decimal('amount', 10, 2);
         $table->date('scheduled_date');

         $table->enum('status', [
            'scheduled',
            'paid',
            'missed',
            'cancelled'
         ])->default('scheduled');

         $table->foreignId('created_by')
            ->constrained('users')
            ->onDelete('restrict');

         $table->timestamp('created_at')->useCurrent();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_schedules');
    }
}
