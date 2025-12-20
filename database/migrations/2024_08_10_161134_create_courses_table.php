<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('courses', function (Blueprint $table) {
         $table->id();
         $table->text('specialization')->nullable();
         $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
         $table->foreignId('university_id')->nullable()->constrained('universities')->nullable()->onDelete('restrict');
         $table->foreignId('department_id')->nullable()->constrained('departments')->nullable()->onDelete('restrict');
         $table->double('fee_threshold', 10, 2)->nullable();
         $table->foreignId('stream_id')->nullable()->constrained('streams')->onDelete('set null');
         $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->onDelete('set null');
         $table->foreignId('promocode')->nullable()->constrained('discounts');
         $table->foreignId('exam_mode_id')->nullable()->constrained('exam_modes')->onDelete('cascade');

         $table->bigInteger('remote_course_id')->nullable();
         $table->enum('remote_status', [
            'active',
            'inactive',
            'discount_pending',
            'course_pending',
            'course_schedule_pending'
         ])->default('inactive');
         $table->text('remote_status_message')->nullable();

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
      Schema::dropIfExists('courses');
   }
}
