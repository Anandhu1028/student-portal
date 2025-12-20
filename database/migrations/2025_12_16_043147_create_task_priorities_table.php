<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskPrioritiesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('task_priorities', function (Blueprint $table) {
         $table->id();
         $table->string('name'); // Low, Medium, High
         $table->integer('level')->nullable(); // 1–5
         $table->string('color')->nullable();
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
      Schema::dropIfExists('task_priorities');
   }
}
