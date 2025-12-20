<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_name')->unique(); // E.g., 'Credit Card', 'Bank Transfer', 'PayPal'
            $table->text('description')->nullable(); // Optional description of the payment method
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
        Schema::dropIfExists('payment_methods');
    }
}
