<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentInteractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_interactions', function (Blueprint $table) {
    $table->id();

    $table->foreignId('student_id')
          ->constrained('students')
          ->cascadeOnDelete();

    $table->foreignId('interaction_type_id')
          ->constrained('interaction_types')
          ->restrictOnDelete();

    // Task manager anchor
    $table->foreignId('task_id')
          ->nullable()
          ->constrained('tasks')
          ->nullOnDelete();

    // Context (mirrors task table)
    $table->foreignId('university_id')
          ->nullable()
          ->constrained('universities')
          ->nullOnDelete();

    $table->foreignId('course_id')
          ->nullable()
          ->constrained('courses')
          ->nullOnDelete();

    $table->foreignId('course_payment_id')
          ->nullable()
          ->constrained('course_payments')
          ->nullOnDelete();

    // Interaction timing
    $table->date('follow_up_date');
    $table->time('follow_up_time')->nullable();
    $table->date('due_date');

    // Notes
    $table->text('remarks')->nullable();

    // Staff who performed the action
    $table->foreignId('performed_by')
          ->nullable()                 // ✅ REQUIRED
          ->constrained('users')
          ->nullOnDelete();            // ✅ SAFE

    $table->boolean('counts_for_performance')->default(true);

    $table->timestamps();

    // Recommended indexes
    $table->index(['student_id', 'follow_up_date']);
    $table->index('performed_by');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_interactions');
    }
}
