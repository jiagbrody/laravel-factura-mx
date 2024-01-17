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
        Schema::create('invoice_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        \JiagBrody\LaravelFacturaMx\Models\InvoiceDocumentType::insert([
            ['id' => \JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum::XML_FILE->value, 'name' => \JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum::XML_FILE->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => \JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum::PDF_FILE->value, 'name' => \JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum::PDF_FILE->getName(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_document_types');
    }
};
