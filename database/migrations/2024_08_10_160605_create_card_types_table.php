<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name')->unique(); // Name of the card type (e.g., Visa, MasterCard)
            $table->text('description')->nullable(); // Optional description of the card type
            $table->integer('start_number')->nullable(); // Optional description of the card type
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
        Schema::dropIfExists('card_types');
    }
}
