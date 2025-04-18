<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::create($tableNames['invoice_incomes'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('invoice_id')->constrained($tableNames['invoices'])->onDelete('cascade');
            // $table->string('version');
            // $table->string('serie')->nullable();
            // $table->string('folio')->nullable();
            // $table->dateTime('fecha');
            $table->string('forma_pago')->nullable();
            // $table->string('condiciones_de_pago')->nullable();
            $table->decimal('sub_total', 24, 6)->nullable();
            $table->decimal('descuento', 24, 6)->nullable();
            $table->string('moneda')->nullable();
            $table->string('tipo_cambio')->nullable();
            $table->decimal('total', 24, 6)->nullable();
            // $table->string('tipo_de_comprobante', 5)->index();
            $table->string('exportacion')->nullable();
            $table->string('metodo_pago')->nullable()->nullable();
            $table->string('lugar_expedicion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('jiagbrody-laravel-factura-mx.table_names');

        Schema::dropIfExists($tableNames['invoice_incomes']);
    }
};
