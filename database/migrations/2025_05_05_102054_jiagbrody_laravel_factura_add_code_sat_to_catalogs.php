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

        Schema::table($tableNames['invoice_payment_types'], function (Blueprint $table) {
            $table->string('sat_code', 5)->default('')->after('name');
        });

        Schema::table($tableNames['invoice_types'], function (Blueprint $table) {
            $table->string('sat_code', 5)->default('')->after('name');
        });

        Schema::table($tableNames['invoice_relationship_types'], function (Blueprint $table) {
            $table->string('sat_code', 5)->default('')->after('name');
            $table->dropColumn('description');
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
