<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'invoice_type_id' => InvoiceTypeEnum::INGRESO->value,
            'invoice_company_id' => InvoiceCompany::factory(),
            'invoice_status_id' => InvoiceStatusEnum::DRAFT->value,
            'invoice_date' => now(),
            'serie' => 'TEST',
            'folio' => (string) fake()->numberBetween(1, 99999),
            'rfc_emisor' => 'EKU9003173C9',
            'rfc_receptor' => 'XAXX010101000',
            'version' => '4.0',
        ];
    }
}
