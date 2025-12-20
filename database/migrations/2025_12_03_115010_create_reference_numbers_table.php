<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferenceNumbersTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('reference_numbers', function (Blueprint $table) {
         $table->id();
         $table->string('type')->unique();  // payment, invoice, receipt, etc.
         $table->bigInteger('current_no')->default(0);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('reference_numbers');
   }
}
