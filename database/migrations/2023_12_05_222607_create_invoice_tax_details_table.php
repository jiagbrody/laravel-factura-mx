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
        Schema::create('invoice_tax_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_tax_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_tax_type_id')->constrained();
            $table->decimal('base', 24, 6);
            $table->string('impuesto', 3);
            $table->string('tipo_factor', 10);
            $table->decimal('tasa_o_cuota', 24, 6)->nullable();
            $table->decimal('importe', 24, 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_tax_details');
    }
};
