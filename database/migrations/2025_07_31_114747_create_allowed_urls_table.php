<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowedUrlsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('allowed_urls', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to the users table
         $table->string('url'); // Foreign key to the urls ta
         $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
         $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
         $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
         $table->foreignId('sub_menu_id')->nullable()->constrained('sub_menus')->onDelete('cascade');
         $table->boolean('allowed')->default(true);
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
      Schema::dropIfExists('allowed_urls');
   }
}
