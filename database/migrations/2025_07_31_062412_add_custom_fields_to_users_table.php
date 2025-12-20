<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomFieldsToUsersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('users', function (Blueprint $table) {
         $table->string('phone_number', 20)->nullable();
         $table->boolean('developer_mode')->default(false)->nullable();
         $table->foreignId('default_menu_id')->default(1)->constrained('menus')->onDelete('cascade');
         $table->foreignId('default_sub_menu_id')->nullable()->constrained('sub_menus')->nullOnDelete();
         $table->foreignId('default_module_id')->default(1)->constrained('modules')->onDelete('cascade');
         $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
         $table->boolean('admin_privilege')->default(false);
         $table->string('timezone', 200)->default('Asia/Kolkata');
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('users', function (Blueprint $table) {
         //
      });
   }
}
