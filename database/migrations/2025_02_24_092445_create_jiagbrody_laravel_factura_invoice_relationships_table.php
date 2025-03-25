<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceRelationshipTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::create($tableNames['invoice_relationship_types'], function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create($tableNames['invoice_relationships'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('origin_invoice_id')->constrained(config('jiagbrody-laravel-factura-mx.table_names.invoices'));
            $table->foreignId('related_invoice_id')->constrained(config('jiagbrody-laravel-factura-mx.table_names.invoices'));
            $table->unsignedBigInteger('invoice_relationship_type_id');
            $table->dateTime('relationship_date');
            $table->timestamps();

            $table->foreign('invoice_relationship_type_id', 'jiagbrody_lfmx_invoice_rel_invoice_rel_type_id_foreign')->references('id')->on($tableNames['invoice_relationship_types']);
        });

        \JiagBrody\LaravelFacturaMx\Models\InvoiceRelationshipType::insert([
            ['id' => InvoiceRelationshipTypeEnum::NOTA_DE_CREDITO->value, 'name' => InvoiceRelationshipTypeEnum::NOTA_DE_CREDITO->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceRelationshipTypeEnum::NOTA_DE_DEBITO->value, 'name' => InvoiceRelationshipTypeEnum::NOTA_DE_DEBITO->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceRelationshipTypeEnum::DEVOLUCION_DE_MERCANCIA->value, 'name' => InvoiceRelationshipTypeEnum::DEVOLUCION_DE_MERCANCIA->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceRelationshipTypeEnum::SUSTITUCION_CFDI_PREVIOS->value, 'name' => InvoiceRelationshipTypeEnum::SUSTITUCION_CFDI_PREVIOS->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceRelationshipTypeEnum::TRASLADOS_DE_MERCANCIAS->value, 'name' => InvoiceRelationshipTypeEnum::TRASLADOS_DE_MERCANCIAS->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceRelationshipTypeEnum::FACTURA_GENERADA_TRASLADOS->value, 'name' => InvoiceRelationshipTypeEnum::FACTURA_GENERADA_TRASLADOS->getName(), 'created_at' => now(), 'updated_at' => now()],
            ['id' => InvoiceRelationshipTypeEnum::CFDI_POR_APLICACION_DE_ANTICIPO->value, 'name' => InvoiceRelationshipTypeEnum::CFDI_POR_APLICACION_DE_ANTICIPO->getName(), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::dropIfExists($tableNames['invoice_relationships']);
        Schema::dropIfExists($tableNames['invoice_relationship_types']);
    }
};
