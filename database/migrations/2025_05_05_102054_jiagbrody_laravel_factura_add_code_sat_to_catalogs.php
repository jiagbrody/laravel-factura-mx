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

        // sat_code ahora se crea junto con los catálogos (migraciones 2024_06_01 y 2025_02_24).
        // En un build fresco esta migración no debe re-agregarlo: guardamos cada cambio con
        // Schema::hasColumn(...) para que sea idempotente sin romper instalaciones MySQL ya migradas.
        if (! Schema::hasColumn($tableNames['invoice_payment_types'], 'sat_code')) {
            Schema::table($tableNames['invoice_payment_types'], function (Blueprint $table) {
                $table->string('sat_code', 5)->default('')->after('name');
            });
        }

        if (! Schema::hasColumn($tableNames['invoice_types'], 'sat_code')) {
            Schema::table($tableNames['invoice_types'], function (Blueprint $table) {
                $table->string('sat_code', 5)->default('')->after('name');
            });
        }

        Schema::table($tableNames['invoice_relationship_types'], function (Blueprint $table) use ($tableNames) {
            if (! Schema::hasColumn($tableNames['invoice_relationship_types'], 'sat_code')) {
                $table->string('sat_code', 5)->default('')->after('name');
            }
            if (Schema::hasColumn($tableNames['invoice_relationship_types'], 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::table($tableNames['invoice_payment_types'], function (Blueprint $table) {
            $table->dropColumn('sat_code');
        });

        Schema::table($tableNames['invoice_types'], function (Blueprint $table) {
            $table->dropColumn('sat_code');
        });

        Schema::table($tableNames['invoice_relationship_types'], function (Blueprint $table) {
            $table->dropColumn('sat_code');
            $table->text('description')->nullable()->after('name');
        });
    }
};
