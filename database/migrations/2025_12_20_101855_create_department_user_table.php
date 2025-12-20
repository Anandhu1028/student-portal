<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_user', function (Blueprint $table) {
             $table->id();
             $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
             $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
             $table->timestamps();

             $table->unique(['department_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_user'):
    }
}
