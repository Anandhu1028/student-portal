<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('students', function (Blueprint $table) {
         $table->id();
         $table->string('track_id')->nullable();
         $table->string('remote_student_id')->unique()->nullable();
         $table->string('first_name');
         $table->string('last_name')->nullable();
         $table->string('fathers_name')->nullable();;
         $table->string('mothers_name')->nullable();;
         $table->foreignId('religion_id')->nullable()->constrained('religions');
         $table->foreignId('religion_category_id')->nullable()->constrained('religion_categories')->onDelete('restrict');
         $table->foreignId('country_id')->nullable()->constrained('countries');
         $table->foreignId('city_id')->nullable()->constrained('cities');
         $table->foreignId('state_id')->nullable()->constrained('states');
         $table->string('postal_code')->nullable();
         $table->foreignId('identity_card_id')->nullable()->constrained('identity_cards')->onDelete('restrict');
         $table->string('identity_card_no')->nullable();
         $table->foreignId('employment_status_id')->nullable()->constrained('employment_statuses')->onDelete('restrict');
         $table->date('date_of_birth')->nullable();
         $table->string('email')->unique();
         $table->string('phone_number')->nullable();
         $table->string('alternative_number')->nullable();
         $table->string('lat')->nullable();
         $table->string('long')->nullable();
         $table->text('address')->nullable();
         $table->string('emergency_contact_name')->nullable();
         $table->string('emergency_contact_phone')->nullable();
         $table->enum('student_status', ['active', 'inactive']);
         $table->string('profile_picture')->nullable();
         $table->integer('profile_completion')->nullable()->default(1);
         $table->enum('gender', ['male', 'female', 'other'])->nullable(); // Adding a 'gender' column that can be null
         $table->foreignId('nationality_id')->nullable()->constrained('countries')->nullable()->onDelete('restrict'); // Link to period names
         $table->foreignId('marital_status_id')->nullable()->constrained('marital_statuses')->onDelete('restrict'); // Link to period names
         $table->foreignId('created_by')->nullable()->constrained('users')->nullable()->onDelete('set null');

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
      Schema::dropIfExists('students');
   }
}
