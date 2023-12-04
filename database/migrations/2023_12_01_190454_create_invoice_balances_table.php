<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_balances', function (Blueprint $table) {
            $table->id();
            $table->decimal('gross_sub_total', 24, 6)->nullable();
            $table->decimal('sub_total', 24, 6)->nullable();
            $table->decimal('discount', 24, 6)->nullable();
            $table->decimal('tax', 24, 6)->nullable();
            $table->decimal('total', 24, 6)->nullable();
            $table->decimal('local_tax', 24, 6)->nullable();
            $table->decimal('balance_total', 24, 6)->nullable();
            $table->boolean('is_paid')->comment('Cuenta liquidada o pagada.');
            $table->foreignId('invoice_payment_type_id')->comment('Tipo de pago: una exhibición o a crédito.')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_balances');
    }
};
