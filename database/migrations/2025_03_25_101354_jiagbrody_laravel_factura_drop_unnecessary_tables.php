<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::dropIfExists($tableNames['invoice_balances']);
        Schema::dropIfExists($tableNames['invoice_details']);
        Schema::dropIfExists($tableNames['invoice_tax_details']);
        Schema::dropIfExists($tableNames['invoice_taxes']);
        Schema::dropIfExists($tableNames['invoice_complement_local_tax_details']);
        Schema::dropIfExists($tableNames['invoice_complement_local_taxes']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::dropIfExists($tableNames['invoice_balances']);
        Schema::dropIfExists($tableNames['invoice_details']);
        Schema::dropIfExists($tableNames['invoice_taxes']);
        Schema::dropIfExists($tableNames['invoice_tax_details']);
        Schema::dropIfExists($tableNames['invoice_complement_local_taxes']);
        Schema::dropIfExists($tableNames['invoice_complement_local_tax_details']);
    }
};
