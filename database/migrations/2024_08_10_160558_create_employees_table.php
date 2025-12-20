<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('emp_code');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone_number')->nullable()->nullable();
            $table->date('hire_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
