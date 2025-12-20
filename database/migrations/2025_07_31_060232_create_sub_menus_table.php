<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubMenusTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('sub_menus', function (Blueprint $table) {
         $table->id();
         $table->string('name', 100);
         $table->string('display_name', 100);
         $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
         $table->string('url', 200)->unique();
         $table->string('icon', 100)->nullable();
         $table->unsignedInteger('order')->default(0);
         $table->boolean('is_active')->default(true);
         $table->boolean('is_documented')->default(false)->nullable();
         $table->boolean('action_menu')->default(false)->nullable();
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
      Schema::dropIfExists('sub_menus');
   }
}
