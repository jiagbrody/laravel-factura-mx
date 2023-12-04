<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JiagBrody\LaravelFacturaMx\Enums\InvoicePaymentTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        \JiagBrody\LaravelFacturaMx\Models\InvoicePaymentType::insert([
            ['id' => InvoicePaymentTypeEnum::PAGO_EN_UNA_EXHIBICION->value, 'name' => InvoicePaymentTypeEnum::PAGO_EN_UNA_EXHIBICION->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoicePaymentTypeEnum::PAGO_A_LINEA_DE_CREDITO->value, 'name' => InvoicePaymentTypeEnum::PAGO_A_LINEA_DE_CREDITO->getName(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payment_types');
    }
};
