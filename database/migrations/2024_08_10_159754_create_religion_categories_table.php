<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReligionCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('religion_categories', function (Blueprint $table) {
            $table->id();
            $table->string('religion_category')->unique(); // Name of the bank
            $table->text('description')->nullable(); // Optional description of the bank
            $table->timestamp('created_at')->useCurrent()->nullable(); // Default value
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('religion_categories');
    }
}
