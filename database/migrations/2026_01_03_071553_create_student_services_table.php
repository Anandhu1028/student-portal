<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_services', function (Blueprint $table) {
         $table->id();

         $table->string('name');           // Marksheet, Certificate, WES
         $table->string('code')->unique(); // MARKSHEET, WES
         $table->boolean('is_chargeable')->default(false);
         $table->decimal('default_fee', 10, 2)->nullable();

         $table->enum('status', ['active', 'inactive'])->default('active');
         $table->timestamp('created_at')->useCurrent();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_services');
    }
}
