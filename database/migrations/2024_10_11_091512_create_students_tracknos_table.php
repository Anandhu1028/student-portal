<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTracknosTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('students_tracknos', function (Blueprint $table) {
         $table->id();
         $table->foreignId('university_id')->nullable()->constrained('universities')->nullable()->onDelete('set null');
         $table->integer('next_number');
         $table->foreignId('branch_id')->nullable()->constrained('branches')->nullable()->onDelete('set null');
         $table->unique(['university_id', 'branch_id'], 'students_tracknos_unique');
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
      Schema::dropIfExists('students_tracknos');
   }
}
