<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskForwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_forwards', function (Blueprint $table) {
    $table->id();

    $table->foreignId('task_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('department_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->foreignId('forwarded_by')
        ->constrained('users');

    $table->date('follow_up_date');

    $table->timestamps();

    $table->unique(['task_id', 'department_id']);
    $table->unique(['task_id', 'user_id']);
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_forwards');
    }
}
