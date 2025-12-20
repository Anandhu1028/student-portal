<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniversities extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('universities', function (Blueprint $table) {
         $table->id();
         $table->bigInteger('country_id')->nullable();
         $table->string('name')->unique();
         $table->string('university_code');
         $table->enum('status', ['active', 'inactive'])->nullable()->default('active');
         $table->boolean('is_global')->default(0);
         $table->tinyInteger('fee_eligibility_pct')->nullable();
         $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->onDelete('set null');
         $table->double('tax_rate')->nullable();
         $table->foreignId('exam_mode_id')->nullable()->constrained('exam_modes')->onDelete('cascade');
         $table->bigInteger('university_id')->nullable();
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
      Schema::dropIfExists('universities_model');
   }
}
