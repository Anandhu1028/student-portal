<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherMenuOperationUrlsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('other_menu_operation_urls', function (Blueprint $table) {
         $table->id();
         $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
         $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
         $table->foreignId('sub_menu_id')->nullable()->constrained('sub_menus')->onDelete('cascade');
         $table->string('name');
         $table->string('url');
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
      Schema::dropIfExists('other_menu_operation_urls');
   }
}
