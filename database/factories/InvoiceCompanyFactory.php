<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

class InvoiceCompanyFactory extends Factory
{
    protected $model = InvoiceCompany::class;

    public function definition(): array
    {
        return [
            'name'                      => 'Emisor 1',
            'rfc'                       => 'EKU9003173C9',
            'nombre'                    => 'ESCUELA KEMPER URGATE',
            'domicilio_fiscal_receptor' => '21855',
            'regimen_fiscal'            => '601',
            'certificate_path'          => '/csd_eku9003173c9_20190617131829/CSD_Sucursal_1_EKU9003173C9_20230517_223850.cer',
            'key_path'                  => '/csd_eku9003173c9_20190617131829/CSD_Sucursal_1_EKU9003173C9_20230517_223850.key',
            'pass_phrase'               => '12345678a',
            'serial_number'             => '30001000000400002434',
        ];
    }
}
