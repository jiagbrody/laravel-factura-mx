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
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::create($tableNames['invoice_cfdi_cancel_receipts'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->unsignedBigInteger('invoice_cfdi_id');
            $table->unsignedBigInteger('invoice_cfdi_cancel_type_id');
            $table->unsignedBigInteger('replacement_invoice_cfdi_id')->nullable()->comment('Para cuando es con clave 01: cancelación relacionada');
            $table->dateTime('receipt_date');
            $table->timestamps();

            $table->foreign('invoice_cfdi_id', 'lfmx_i_cfdi_c_receipts_invoice_cfdi_id_foreign')->references('id')->on($tableNames['invoice_cfdis']);
            $table->foreign('invoice_cfdi_cancel_type_id', 'lfmx_i_cfdi_c_receipts_invoice_cfdi_cancel_type_id_foreign')->references('id')->on($tableNames['invoice_cfdi_cancel_types']);
            $table->foreign('replacement_invoice_cfdi_id', 'lfmx_i_cfdi_c_receipts_replacement_invoice_cfdi_id_foreign')->references('id')->on($tableNames['invoice_cfdis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::dropIfExists($tableNames['invoice_cfdi_cancel_receipts']);
    }
};
