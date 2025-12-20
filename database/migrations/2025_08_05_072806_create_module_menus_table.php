<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleMenusTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('module_menus', function (Blueprint $table) {
         $table->id();
         $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
         $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
         $table->unique(['module_id', 'menu_id'], 'user_menu_submenu_unique');
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
      Schema::dropIfExists('module_menus');
   }
}
