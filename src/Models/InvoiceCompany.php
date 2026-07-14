<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $rfc
 * @property string $nombre razón social fiscal
 * @property string $domicilio_fiscal
 * @property string|null $residencia_fiscal
 * @property string|null $num_reg_id_trib
 * @property string $regimen_fiscal
 * @property string $certificate_path
 * @property string $key_path
 * @property string $pass_phrase
 * @property string $serial_number
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
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
            'domicilio_fiscal',
            'residencia_fiscal',
            'num_reg_id_trib',
            'regimen_fiscal',
        ]);
    }
}
