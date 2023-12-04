<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(config('factura-mx.foreign_id_related_to_invoices'))->nullable()->comment('estado de cuenta sobre un modelo de negocio')->index();
            $table->foreignId('user_id')->comment('El usuario quien crea la factura')->constrained();
            $table->foreignId('invoice_type_id')->comment('Tipo de comprobante del SAT (ingreso, egreso, traslado...)')->constrained();
            $table->foreignId('invoice_company_id')->comment('A que empresa se le factura (emisor)')->constrained();
            $table->foreignId('invoice_status_id')->default(\JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum::DRAFT->value);
            $table->nullableMorphs('invoiceable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
