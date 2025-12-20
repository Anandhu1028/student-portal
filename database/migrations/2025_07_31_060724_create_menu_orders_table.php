<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuOrdersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('menu_orders', function (Blueprint $table) {
         $table->id();
         $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
         $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
         $table->unsignedInteger('order')->default(0);
         $table->unique(['module_id', 'menu_id'], 'menu_orders_unique');
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
      Schema::dropIfExists('menu_orders');
   }
}
