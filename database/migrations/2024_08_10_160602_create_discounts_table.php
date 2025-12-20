<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('discounts', function (Blueprint $table) {
         $table->id(); // Primary key
         $table->string('promocode');
         $table->text('description')->nullable();
         $table->date('start_date');
         $table->date('end_date')->nullable();
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
      Schema::dropIfExists('discounts');
   }
}
