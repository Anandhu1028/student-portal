<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('notifications', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
         $table->text('notification_header');
         $table->string('class_name');
         $table->text('params');
         $table->text('notification_message');
         $table->string('notification_link')->nullable();
         $table->boolean('is_read')->default(0);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('notifications');
   }
}
