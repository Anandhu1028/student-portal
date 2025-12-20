<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserModulesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('user_modules', function (Blueprint $table) {
         $table->id();
         $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
         $table->foreignId('user_id')->constrained()->onDelete('cascade');
         $table->timestamp('created_at')->useCurrent();
         $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('user_modules');
   }
}
