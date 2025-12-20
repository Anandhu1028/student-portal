<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationalQualificationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('educational_qualifications', function (Blueprint $table) {
         $table->id();
         $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
         $table->foreignId('institution_id')->nullable()->constrained('universities')->onDelete('restrict'); // Link to period names
         $table->string('field_of_study')->nullable();
         $table->string('graduation_year', 10)->nullable();
         $table->string('gpa', 20)->nullable();
         $table->string('other_degree_name', 50)->nullable();
         $table->string('other_college_name', 50)->nullable();
         $table->timestamp('created_at')->useCurrent()->nullable(); // Default value
         $table->foreignId('degree_id')->nullable()->constrained('streams')->onDelete('cascade'); // Link to period names
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
      Schema::dropIfExists('educational_qualifications');
   }
}
