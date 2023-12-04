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
        Schema::create('invoice_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rfc', 20);
            $table->string('nombre', 254)->comment('razon social fiscal');
            $table->string('domicilio_fiscal_receptor', 10);
            $table->string('residencia_fiscal', 10)->nullable();
            $table->string('num_reg_id_trib', 10)->nullable();
            $table->string('regimen_fiscal', 10);
            $table->string('uso_cfdi', 10);
            $table->boolean('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_companies');
    }
};
