<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCompany extends Model
{
    use HasFactory;

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_companies', parent::getTable());
    }

    public static function getUnprotectedData($invoiceCompanyId): InvoiceCompany
    {
        return InvoiceCompany::query()->where('id', $invoiceCompanyId)->firstOrFail([
            'id',
            'name',
            'rfc',
            'nombre',
            'domicilio_fiscal_receptor',
            'residencia_fiscal',
            'num_reg_id_trib',
            'regimen_fiscal',
        ]);
    }
}
