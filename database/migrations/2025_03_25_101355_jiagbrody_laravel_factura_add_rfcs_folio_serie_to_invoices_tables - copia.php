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

        Schema::table($tableNames['invoices'], function (Blueprint $table) {
            $table->string('serie')->after('invoice_date');
            $table->string('folio')->after('serie');
            $table->string('rfc_emisor')->default('')->after('invoice_status_id');
            $table->string('rfc_receptor')->default('')->after('rfc_emisor');
            $table->string('version')->default('')->after('rfc_receptor');

            $table->index(['invoice_date', 'serie', 'folio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::table($tableNames['invoices'], function (Blueprint $table) {
            $table->dropColumn('serie');
            $table->dropColumn('folio');
            $table->dropColumn('rfc_emisor');
            $table->dropColumn('rfc_receptor');
            $table->dropColumn('version');
            $table->dropIndex(['invoice_date', 'serie', 'folio']);
        });
    }
};
