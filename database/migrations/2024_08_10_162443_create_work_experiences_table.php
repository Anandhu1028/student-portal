<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('job_title');
            $table->string('company_name');
            $table->text('job_description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('currently_employed')->default(false); // Indicates if still employed
            $table->decimal('salary', 10, 2)->nullable(); // Salary, if applicable
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
        Schema::dropIfExists('work_experiences');
    }
}
