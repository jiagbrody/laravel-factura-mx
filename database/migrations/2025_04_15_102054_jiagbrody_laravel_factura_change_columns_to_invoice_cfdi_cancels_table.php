<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * En instalaciones antiguas elimina la columna heredada
     * "invoice_cfdi_cancel_type_id" (con su FK). En builds frescos la columna
     * ya no existe (la migración de creación dejó de definirla), por lo que
     * todo va protegido con hasColumn(). El dropForeign solo corre en drivers
     * que lo soportan (SQLite no permite eliminar FKs).
     */
    public function up(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');
        $cancelsTable = $tableNames['invoice_cfdi_cancels'];

        if (Schema::hasColumn($cancelsTable, 'invoice_cfdi_cancel_type_id')) {
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                Schema::table($cancelsTable, function (Blueprint $table) {
                    $table->dropForeign('lfmx_invoice_cfdi_cancels_invoice_cfdi_cancel_type_id_foreign');
                });
            }

            Schema::table($cancelsTable, function (Blueprint $table) {
                $table->dropColumn('invoice_cfdi_cancel_type_id');
            });
        }

        if (! Schema::hasColumn($cancelsTable, 'invoice_cfdi_cancel_receipt_id')) {
            Schema::table($cancelsTable, function (Blueprint $table) use ($tableNames) {
                $table->unsignedBigInteger('invoice_cfdi_cancel_receipt_id')->after('invoice_cfdi_id');
                $table->foreign('invoice_cfdi_cancel_receipt_id', 'lfmx_i_cfdi_c_receipts_invoice_cfdi_cancel_receipt_id_foreign')->references('id')->on($tableNames['invoice_cfdi_cancel_receipts']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');
        $cancelsTable = $tableNames['invoice_cfdi_cancels'];

        if (! Schema::hasColumn($cancelsTable, 'invoice_cfdi_cancel_type_id')) {
            Schema::table($cancelsTable, function (Blueprint $table) use ($tableNames) {
                $table->unsignedBigInteger('invoice_cfdi_cancel_type_id');
                $table->foreign('invoice_cfdi_cancel_type_id', 'lfmx_invoice_cfdi_cancels_invoice_cfdi_cancel_type_id_foreign')->references('id')->on($tableNames['invoice_cfdi_cancel_types']);
            });
        }

        if (Schema::hasColumn($cancelsTable, 'invoice_cfdi_cancel_receipt_id')) {
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                Schema::table($cancelsTable, function (Blueprint $table) {
                    $table->dropForeign('lfmx_i_cfdi_c_receipts_invoice_cfdi_cancel_receipt_id_foreign');
                });
            }

            Schema::table($cancelsTable, function (Blueprint $table) {
                $table->dropColumn('invoice_cfdi_cancel_receipt_id');
            });
        }
    }
};
