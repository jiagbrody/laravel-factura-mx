<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');
        Schema::table($tableNames['invoices'], function (Blueprint $table) {
            $table->dateTime('invoice_date')->index()->default(now())->after('user_id');
        });
    }

    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');
        Schema::table($tableNames['invoices'], function (Blueprint $table) {
            $table->dropColumn('invoice_date');
        });
    }
};
