<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing unsigned bigint primary key column
            $table->string('name'); // Example column for the degree name
            $table->string('code', 10, 2); // Example column for the degree name

            $table->enum('status', ['active', 'inactive'])->nullable()->default('active'); // Example column for a unique code (optional)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('streams');
    }
}
