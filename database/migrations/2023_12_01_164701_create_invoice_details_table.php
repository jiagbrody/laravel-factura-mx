<?php declare(strict_types=1);

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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('version');
            $table->string('serie')->nullable();
            $table->string('folio')->nullable();
            $table->dateTime('fecha');
            $table->string('forma_pago')->index()->nullable();
            $table->string('condiciones_de_pago')->nullable();
            $table->decimal('sub_total', 24, 6);
            $table->decimal('descuento', 24, 6)->nullable();
            $table->string('moneda');
            $table->string('tipo_cambio')->nullable();
            $table->decimal('total', 24, 6);
            $table->string('tipo_de_comprobante', 5)->index();
            $table->string('exportacion');
            $table->string('metodo_pago')->index()->nullable();
            $table->string('lugar_expedicion');
            $table->string('receptor_rfc', 20)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
