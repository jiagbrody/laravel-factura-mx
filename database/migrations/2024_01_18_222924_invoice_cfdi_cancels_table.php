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
        Schema::create('invoice_cfdi_cancels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_cfdi_id')->constrained();
            $table->foreignId('invoice_cfdi_cancel_type_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_cfdi_cancels');
    }
};
