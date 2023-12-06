<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTaxTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_tax_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        \JiagBrody\LaravelFacturaMx\Models\InvoiceTaxType::insert([
            ['id' => InvoiceTaxTypeEnum::TRASLADO->value, 'name' => InvoiceTaxTypeEnum::TRASLADO->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceTaxTypeEnum::RETENCION->value, 'name' => InvoiceTaxTypeEnum::RETENCION->getName(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_tax_types');
    }
};
