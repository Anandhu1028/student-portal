<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('roles', function (Blueprint $table) {
         $table->id();
         $table->string('role_name')->unique();
         $table->string('unique_key')->nullable()->unique();
         $table->text('description')->nullable();
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
      Schema::dropIfExists('roles');
   }
}
