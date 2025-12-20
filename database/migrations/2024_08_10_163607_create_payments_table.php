<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('payments', function (Blueprint $table) {
         $table->id();
         $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
         $table->string('student_track_id')->nullable();
         $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
         $table->enum('payment_type', ['Repayment', 'Deposit', 'Refund', 'Transfer', 'Downpayment', 'Initial'])->nullable()->default('Repayment');
         $table->decimal('amount', 10, 2);
         $table->decimal('tax_amount', 10, 2);
         // total amount is amount + tax amount
         $table->decimal('discount_amount', 10, 2)->nullable();
         $table->foreignId('promocode')->nullable()->constrained('discounts')->onDelete('set null')->nullable(); // Link to terminals
         $table->date('payment_date');
         $table->foreignId('terminal_id')->nullable()->constrained('terminals')->onDelete('restrict')->default(1); // Link to terminals
         $table->foreignId('card_type_id')->nullable()->constrained('card_types')->onDelete('restrict'); // Link to card types
         $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('restrict'); // Link to banks
         $table->foreignId('currency_id')->nullable()->constrained('countries')->onDelete('set null');
         $table->string('transaction_ref', 50)->nullable();
         $table->string('reference_no')->nullable();
         $table->text('notes')->nullable(); // Additional notes or comments about the payment
         $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('restrict');
         $table->foreignId('course_schedule_id')->nullable()->constrained('course_schedules')->onDelete('restrict');
         $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->onDelete('set null');
         $table->enum('status', ['active', 'reversed'])->nullable()->default('active');
         $table->timestamp('created_at')->useCurrent()->nullable(); // Default value
         $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable(); // Default value with a
         $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
         $table->foreignId('collected_for')->nullable()->constrained('users')->onDelete('set null');
         $table->date('next_payment_date')->nullable();
         $table->boolean('is_closed')->default(0);
         $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('cascade');
         $table->integer('payment_remote_id')->unique()->nullable();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('payments');
   }
}
