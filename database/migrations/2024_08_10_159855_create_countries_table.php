<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('countries', function (Blueprint $table) {
         $table->id();
         $table->string('code')->unique();
         $table->string('name');
         $table->string('currency_code')->nullable();
         $table->string('currency_name')->nullable();
         $table->enum('status', ['active', 'inactive'])->default('active');
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
      Schema::dropIfExists('countries');
   }
}
