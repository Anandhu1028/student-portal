<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('modules', function (Blueprint $table) {
         $table->id();
         $table->string('name', 100);
         $table->string('app_name', 100);
         $table->text('description')->nullable();
         $table->string('url', 200)->nullable();
         $table->string('icon', 100)->nullable();
         $table->string('image_path')->nullable();
         $table->unsignedInteger('order')->default(0);
         $table->boolean('is_active')->default(true);
         $table->string('timezone', 32)->default('UTC');
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
      Schema::dropIfExists('modules');
   }
}
