<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('period_names', function (Blueprint $table) {
            //installment period name
            $table->id();
            $table->string('name')->unique(); // Name of the period, must be unique
            $table->text('description')->nullable(); // Optional description of the period
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
        Schema::dropIfExists('period_names');
    }
}
