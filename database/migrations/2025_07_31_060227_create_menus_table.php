<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('menus', function (Blueprint $table) {
         $table->id();
         $table->string('name', 100);
         $table->string('display_name', 100);
         $table->foreignId('module_id')->nullable()->constrained('modules')->nullOnDelete();
         $table->foreignId('menu_title_id')->nullable()->constrained('menu_titles')->onDelete('cascade');
         $table->string('url', 200)->nullable();
         $table->string('icon', 100)->nullable();
         $table->boolean('is_active')->default(true);
         $table->boolean('is_documented')->default(false)->nullable();
         $table->boolean('action_menu')->default(false)->nullable();
         $table->boolean('default_menu')->default(false)->nullable();
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
      Schema::dropIfExists('menus');
   }
}
