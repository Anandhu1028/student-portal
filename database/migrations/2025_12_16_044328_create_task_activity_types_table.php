   <?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   class CreateTaskActivityTypesTable extends Migration
   {
      /**
       * Run the migrations.
      *
      * @return void
      */
      public function up()
      {
         Schema::create('task_activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Model class
            $table->string('icon_path'); // Model class
            $table->boolean('is_internal_event')->default(false); // Model class
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
         Schema::dropIfExists('task_activity_types');
      }
   }
