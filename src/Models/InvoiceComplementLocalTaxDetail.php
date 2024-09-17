<?php

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceComplementLocalTaxDetail extends Model
{
    use HasFactory;

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_complement_local_tax_details', parent::getTable());
    }

    public function invoiceComplementTax(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceComplementLocalTax::class);
    }
}
