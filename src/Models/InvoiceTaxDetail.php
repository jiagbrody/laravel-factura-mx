<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTaxDetail extends Model
{
    use HasFactory;

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_tax_details', parent::getTable());
    }

    public function invoiceTax(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceTax::class);
    }
}
