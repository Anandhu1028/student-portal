<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxRatesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('tax_rates', function (Blueprint $table) {
         $table->id();
         $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
         $table->tinyInteger('tax_rate');
         $table->boolean('is_active')->default(1);
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
      Schema::dropIfExists('tax_rates');
   }
}
