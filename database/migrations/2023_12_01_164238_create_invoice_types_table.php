<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        \JiagBrody\LaravelFacturaMx\Models\InvoiceType::insert([
            ['id' => InvoiceTypeEnum::INGRESO->value, 'name' => InvoiceTypeEnum::INGRESO->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceTypeEnum::EGRESO->value, 'name' => InvoiceTypeEnum::EGRESO->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceTypeEnum::TRASLADO->value, 'name' => InvoiceTypeEnum::TRASLADO->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceTypeEnum::NOMINA->value, 'name' => InvoiceTypeEnum::NOMINA->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceTypeEnum::PAGO->value, 'name' => InvoiceTypeEnum::PAGO->getName(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_types');
    }
};
