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
        Schema::create('invoice_statement_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger(config('factura-mx.foreign_id_related_to_concepts'))->nullable()->comment('conceptos de la factura relacionados sobre un modelo de negocio')->index();
            $table->unsignedSmallInteger('quantity')->default(0);
            $table->decimal('unit_price', 24, 6)->default(0);
            $table->decimal('gross_sub_total', 24, 6)->default(0);
            $table->decimal('discount', 24, 6)->default(0);
            $table->decimal('sub_total', 24, 6)->default(0);
            $table->decimal('tax', 24, 6)->default(0);
            $table->decimal('total', 24, 6)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_statement_detail');
    }
};
