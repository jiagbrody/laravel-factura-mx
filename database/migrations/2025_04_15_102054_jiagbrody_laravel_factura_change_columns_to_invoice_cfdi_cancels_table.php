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

        Schema::table($tableNames['invoice_cfdi_cancels'], function (Blueprint $table) use ($tableNames) {
            $table->dropForeign('lfmx_invoice_cfdi_cancels_invoice_cfdi_cancel_type_id_foreign');
            // $table->dropIndex('lfmx_invoice_cfdi_cancels_invoice_cfdi_cancel_type_id_foreign');
            $table->dropColumn('invoice_cfdi_cancel_type_id');

            $table->unsignedBigInteger('invoice_cfdi_cancel_receipt_id')->after('invoice_cfdi_id');
            $table->foreign('invoice_cfdi_cancel_receipt_id', 'lfmx_i_cfdi_c_receipts_invoice_cfdi_cancel_receipt_id_foreign')->references('id')->on($tableNames['invoice_cfdi_cancel_receipts']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::table($tableNames['invoice_cfdi_cancels'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('invoice_cfdi_cancel_type_id');
            $table->foreign('invoice_cfdi_cancel_type_id', 'lfmx_invoice_cfdi_cancels_invoice_cfdi_cancel_type_id_foreign')->references('id')->on($tableNames['invoice_cfdi_cancel_types']);

            $table->dropForeign('lfmx_i_cfdi_c_receipts_invoice_cfdi_cancel_receipt_id_foreign');
            $table->dropIndex('lfmx_i_cfdi_c_receipts_invoice_cfdi_cancel_receipt_id_foreign');
            $table->dropColumn('invoice_cfdi_cancel_receipt_id');
        });
    }
};
