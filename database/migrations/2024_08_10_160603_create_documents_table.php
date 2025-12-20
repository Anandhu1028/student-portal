<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('documents', function (Blueprint $table) {
         $table->id();
         $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
         $table->string('document_path')->nullable();;
         $table->string('document_number')->unique()->nullable();
         $table->foreignId('doc_category_id')->constrained('document_categories')->nullable()->onDelete('restrict'); // Link to period names
         $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Document status
         $table->string('uploaded_by'); // User ID or name of the person who uploaded the document
         $table->boolean('is_confidential')->nullable()->default(false);
         $table->date('expiry_date')->nullable();
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
      Schema::dropIfExists('documents');
   }
}
