<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskStatusesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('task_statuses', function (Blueprint $table) {
         $table->id();
         $table->string('name');
         $table->string('slug')->unique();
         $table->boolean('is_initial')->default(false);
         $table->boolean('is_terminal')->default(false);
         $table->integer('sort_order')->default(0);
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
      Schema::dropIfExists('task_statuses');
   }
}
