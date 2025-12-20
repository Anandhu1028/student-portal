<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskRelationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('task_relations', function (Blueprint $table) {
         // Not required for now
         $table->id();

         $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();

         $table->string('related_type'); // Model class
         $table->unsignedBigInteger('related_id');

         $table->string('relation_type')->nullable(); // primary, reference

         $table->timestamps();

         $table->index(['related_type', 'related_id']);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('task_relations');
   }
}
