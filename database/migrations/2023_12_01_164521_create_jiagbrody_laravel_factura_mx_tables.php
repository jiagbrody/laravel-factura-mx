<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::create($tableNames['invoice_companies'], function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rfc', 20);
            $table->string('nombre', 254)->comment('razon social fiscal');
            $table->string('domicilio_fiscal_receptor', 10);
            $table->string('residencia_fiscal', 10)->nullable();
            $table->string('num_reg_id_trib', 10)->nullable();
            $table->string('regimen_fiscal', 10);
            $table->text('certificate_path')->comment('ruta del cetificado del sat para timbrar (.cer)');
            $table->text('key_path')->comment('ruta de la llave del sat para timbrar (.key)');
            $table->string('pass_phrase')->comment('contraseña del archivo de la llave');
            $table->string('serial_number')->comment('numero serial del archivo de la llave');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create($tableNames['invoice_types'], function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        Schema::create($tableNames['invoice_statuses'], function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        Schema::create($tableNames['invoices'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('user_id')->comment('El usuario quien crea la factura')->constrained();
            $table->unsignedBigInteger('invoice_type_id')->comment('Tipo de comprobante del SAT (ingreso, egreso, traslado...)');
            $table->unsignedBigInteger('invoice_company_id')->comment('A que empresa se le factura (emisor)');
            $table->unsignedBigInteger('invoice_status_id')->comment('estatus de la factura (vigente, cancelado, etc)');
            $table->nullableMorphs('invoiceable');
            $table->timestamps();

            $table->foreign('invoice_type_id', 'invoices_invoice_type_id_foreign')->references('id')->on($tableNames['invoice_types'])->onDelete('cascade');
            $table->foreign('invoice_company_id', 'invoices_invoice_company_id_foreign')->references('id')->on($tableNames['invoice_companies'])->onDelete('cascade');
            $table->foreign('invoice_status_id', 'invoices_invoice_status_id_foreign')->references('id')->on($tableNames['invoice_statuses'])->onDelete('cascade');
        });

        Schema::create($tableNames['invoice_details'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->unique();
            $table->string('version');
            $table->string('serie')->nullable();
            $table->string('folio')->nullable();
            $table->dateTime('fecha');
            $table->string('forma_pago')->index()->nullable();
            $table->string('condiciones_de_pago')->nullable();
            $table->decimal('sub_total', 24, 6)->nullable();
            $table->decimal('descuento', 24, 6)->nullable();
            $table->string('moneda')->nullable();
            $table->string('tipo_cambio')->nullable();
            $table->decimal('total', 24, 6)->nullable();
            $table->string('tipo_de_comprobante', 5)->index();
            $table->string('exportacion')->nullable();
            $table->string('metodo_pago')->index()->nullable()->nullable();
            $table->string('lugar_expedicion')->nullable();
            $table->string('receptor_rfc', 20)->index();
            $table->timestamps();

            $table->foreign('invoice_id', 'invoice_details_invoice_id_foreign')->references('id')->on($tableNames['invoices'])->onDelete('cascade');
        });

        Schema::create($tableNames['invoice_payment_types'], function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        Schema::create($tableNames['invoice_balances'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->unique();
            $table->unsignedBigInteger('invoice_payment_type_id')->comment('Tipo de pago: una exhibición o a crédito.');
            $table->decimal('gross_sub_total', 24, 6)->nullable();
            $table->decimal('sub_total', 24, 6)->nullable();
            $table->decimal('discount', 24, 6)->nullable();
            $table->decimal('tax', 24, 6)->nullable();
            $table->decimal('total', 24, 6)->nullable();
            $table->decimal('local_tax', 24, 6)->nullable();
            $table->decimal('balance_total', 24, 6)->nullable();
            $table->boolean('is_paid')->comment('Cuenta liquidada o pagada.');
            $table->timestamps();

            $table->foreign('invoice_id', 'invoice_balances_details_invoice_id_foreign')->references('id')->on($tableNames['invoices'])->onDelete('cascade');
            $table->foreign('invoice_payment_type_id', 'invoice_balances_invoice_payment_type_id_foreign')->references('id')->on($tableNames['invoice_payment_types'])->onDelete('cascade');
        });

        Schema::create($tableNames['invoice_tax_types'], function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->timestamps();
        });

        Schema::create($tableNames['invoice_taxes'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->unique();
            $table->decimal('total_impuestos_retenidos', 24, 6)->nullable();
            $table->decimal('total_impuestos_trasladados', 24, 6)->nullable();
            $table->timestamps();

            $table->foreign('invoice_id', 'invoice_taxes_invoice_id_foreign')->references('id')->on($tableNames['invoices'])->onDelete('cascade');
        });

        Schema::create($tableNames['invoice_tax_details'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->unsignedBigInteger('invoice_tax_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('invoice_tax_type_id')->constrained();
            $table->decimal('base', 24, 6);
            $table->string('impuesto', 3);
            $table->string('tipo_factor', 10);
            $table->decimal('tasa_o_cuota', 24, 6)->nullable();
            $table->decimal('importe', 24, 6)->nullable();
            $table->timestamps();

            $table->foreign('invoice_tax_id', 'invoice_tax_details_invoice_tax_id_foreign')->references('id')->on($tableNames['invoice_taxes'])->onDelete('cascade');
            $table->foreign('invoice_tax_type_id', 'invoice_tax_details_invoice_tax_type_id_foreign')->references('id')->on($tableNames['invoice_tax_types']);
        });

        Schema::create($tableNames['invoice_statement_detail'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger(config('jiagbrody-laravel-factura-mx.column_names.foreign_id_related_to_concepts'))->nullable()->comment('conceptos de la factura relacionados sobre un modelo de negocio')->index('invoice_statement_detail_statement_detail_id_index');
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->decimal('unit_price', 24, 6)->default(0);
            $table->decimal('gross_sub_total', 24, 6)->default(0);
            $table->decimal('discount', 24, 6)->default(0);
            $table->decimal('sub_total', 24, 6)->default(0);
            $table->decimal('tax', 24, 6)->default(0);
            $table->decimal('total', 24, 6)->default(0);

            $table->foreign('invoice_id', 'invoice_statement_detail_invoice_id_foreign')->references('id')->on($tableNames['invoices'])->onDelete('cascade');
        });

        Schema::create($tableNames['invoice_document_types'], function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create($tableNames['invoice_documents'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->unsignedBigInteger('invoice_document_type_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->string('extension');
            $table->string('storage')->comment('localización del disco donde se guarda');
            $table->morphs('documentable', 'invoice_documents_documentable_type_documentable_id_index');
            $table->timestamps();

            $table->foreign('invoice_document_type_id', 'invoice_documents_invoice_document_type_id_foreign')->references('id')->on($tableNames['invoice_document_types']);
        });

        Schema::create($tableNames['invoice_cfdi_cancel_types'], function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create($tableNames['invoice_cfdis'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('invoice_id');
            $table->uuid();
            $table->timestamps();

            $table->foreign('invoice_id', 'invoice_cfdis_invoice_id_foreign')->references('id')->on($tableNames['invoices'])->onDelete('cascade');
        });

        Schema::create($tableNames['invoice_cfdi_cancels'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->unsignedBigInteger('invoice_cfdi_id');
            $table->unsignedBigInteger('invoice_cfdi_cancel_type_id');
            $table->timestamps();

            $table->foreign('invoice_cfdi_id', 'invoice_cfdi_cancels_invoice_cfdi_id_foreign')->references('id')->on($tableNames['invoice_cfdis']);
            $table->foreign('invoice_cfdi_cancel_type_id', 'invoice_cfdi_cancels_invoice_cfdi_cancel_type_id_foreign')->references('id')->on($tableNames['invoice_cfdi_cancel_types']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::dropIfExists($tableNames['invoice_balances']);
        Schema::dropIfExists($tableNames['invoice_cfdi_cancels']);
        Schema::dropIfExists($tableNames['invoice_cfdis']);
        Schema::dropIfExists($tableNames['invoice_details']);
        Schema::dropIfExists($tableNames['invoice_statement_detail']);
        Schema::dropIfExists($tableNames['invoice_tax_details']);
        Schema::dropIfExists($tableNames['invoice_taxes']);
        Schema::dropIfExists($tableNames['invoices']);
        Schema::dropIfExists($tableNames['invoice_companies']);
        Schema::dropIfExists($tableNames['invoice_types']);
        Schema::dropIfExists($tableNames['invoice_statuses']);
        Schema::dropIfExists($tableNames['invoice_payment_types']);
        Schema::dropIfExists($tableNames['invoice_tax_types']);
        Schema::dropIfExists($tableNames['invoice_documents']);
        Schema::dropIfExists($tableNames['invoice_document_types']);
        Schema::dropIfExists($tableNames['invoice_cfdi_cancel_types']);
    }
};
