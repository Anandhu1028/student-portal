<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseSchedulesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('course_schedules', function (Blueprint $table) {
         $table->id();
         $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
         $table->foreignId('university_id')->constrained('universities')->onDelete('cascade');
         $table->string('batch_name')->nullable();
         $table->foreignId('course_batch_type_id')->nullable()->constrained('course_batch_types')->onDelete('cascade');
         $table->double('course_fee');
         $table->double('other_fees', 10, 2)->nullable();
         $table->double('fee_threshold', 10, 2);
         $table->double('commission', 10, 2)->nullable();
         $table->date('start_date');
         $table->date('end_date');
         $table->date('altered_end_date')->nullable(); // changed from edited_end_date to be more descriptive
         $table->string('tenure_duration')->nullable(); // changed from tenure for clarity
         $table->foreignId('promocode_id')
            ->nullable()
            ->constrained('discounts')
            ->onDelete('set null');
         $table->foreignId('exam_mode_id')->nullable()->constrained('exam_modes')->onDelete('cascade');
         $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->onDelete('set null');
         $table->tinyInteger('fee_eligibility_pct')->nullable();
         $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
         $table->boolean('admission_closed')->default(0);
         $table->date('admission_deadline')->nullable();
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
      Schema::dropIfExists('course_schedules');
   }
}
