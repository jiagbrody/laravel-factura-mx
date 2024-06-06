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
        if (!Schema::hasTable('invoice_companies')) {
            Schema::create('invoice_companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('rfc', 20);
                $table->string('nombre', 254)->comment('razon social fiscal');
                $table->string('domicilio_fiscal_receptor', 10);
                $table->string('residencia_fiscal', 10)->nullable();
                $table->string('num_reg_id_trib', 10)->nullable();
                $table->string('regimen_fiscal', 10);
                $table->text('certificate_path')->comment('ruta del cetificado del sat para timbrar (.cer)');
                $table->text('key_path')->comment('ruta de la llave del sat para timbrar (.key)');
                $table->string('pass_phrase')->comment('contraseña del archivo de la llave');
                $table->string('serial_number')->comment('numero serial del archivo de la llave');
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        \JiagBrody\LaravelFacturaMx\Models\InvoiceCompany::insert([
            [
                'name' => 'Emisor 1',
                'rfc' => 'EKU9003173C9',
                'nombre' => 'ESCUELA KEMPER URGATE',
                'domicilio_fiscal_receptor' => '21855',
                'regimen_fiscal' => '601',
                'certificate_path' => '/csd_eku9003173c9_20190617131829/CSD_Sucursal_1_EKU9003173C9_20230517_223850.cer',
                'key_path' => '/csd_eku9003173c9_20190617131829/CSD_Sucursal_1_EKU9003173C9_20230517_223850.key',
                'pass_phrase' => '12345678a',
                'serial_number' => '30001000000400002434',
            ],
            [
                'name' => 'Emisor 2',
                'rfc' => 'IIA040805DZ4',
                'nombre' => 'INDISTRIA ILUMINADORA DE ALMACENES',
                'domicilio_fiscal_receptor' => '63900',
                'regimen_fiscal' => '601',
                'certificate_path' => '/csd_iia040805dz4_20190617133200/CSD_Sucursal_1_IIA040805DZ4_20230518_062510.cer',
                'key_path' => '/csd_iia040805dz4_20190617133200/CSD_Sucursal_1_IIA040805DZ4_20230518_062510.key',
                'pass_phrase' => '12345678a',
                'serial_number' => '30001000000400002447',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_companies');
    }
};
