<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * user_id pasa a nullable: el timbrado puede correr en colas o comandos
     * artisan sin usuario autenticado (auth()->id() = null).
     *
     * En builds frescos la migración de creación ya define las columnas como
     * nullable; este ALTER es para instalaciones existentes y es idempotente.
     * SQLite se omite (no soporta estos ALTER y sus builds siempre son frescos).
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        foreach ($this->tablesWithUserId() as $tableName) {
            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE {$tableName} ALTER COLUMN user_id DROP NOT NULL");
            } else {
                DB::statement("ALTER TABLE {$tableName} MODIFY user_id BIGINT UNSIGNED NULL");
            }
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        foreach ($this->tablesWithUserId() as $tableName) {
            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE {$tableName} ALTER COLUMN user_id SET NOT NULL");
            } else {
                DB::statement("ALTER TABLE {$tableName} MODIFY user_id BIGINT UNSIGNED NOT NULL");
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function tablesWithUserId(): array
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names', []);

        return [
            $tableNames['invoices'] ?? 'jiagbrody_lfmx_invoices',
            $tableNames['invoice_cfdis'] ?? 'jiagbrody_lfmx_invoice_cfdis',
            $tableNames['invoice_incidents'] ?? 'jiagbrody_lfmx_invoice_incidents',
        ];
    }
};
