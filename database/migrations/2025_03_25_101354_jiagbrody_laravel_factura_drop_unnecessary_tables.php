<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Elimina tablas heredadas. Algunas claves (p. ej. "invoice_balances") ya
     * no existen en el config actual del paquete, por lo que se usa el nombre
     * histórico por defecto como respaldo para no romper builds frescos.
     */
    public function up(): void
    {
        foreach ($this->legacyTableNames() as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->legacyTableNames() as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }

    /**
     * @return array<int, string>
     */
    private function legacyTableNames(): array
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names', []);

        return [
            $tableNames['invoice_balances'] ?? 'jiagbrody_lfmx_invoice_balances',
            $tableNames['invoice_details'] ?? 'jiagbrody_lfmx_invoice_details',
            $tableNames['invoice_tax_details'] ?? 'jiagbrody_lfmx_invoice_tax_details',
            $tableNames['invoice_taxes'] ?? 'jiagbrody_lfmx_invoice_taxes',
            $tableNames['invoice_complement_local_tax_details'] ?? 'jiagbrody_lfmx_invoice_complement_local_tax_details',
            $tableNames['invoice_complement_local_taxes'] ?? 'jiagbrody_lfmx_invoice_complement_local_taxes',
        ];
    }
};
