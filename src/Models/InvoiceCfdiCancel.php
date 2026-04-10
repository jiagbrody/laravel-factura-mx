<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InvoiceCfdiCancel extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_cfdi_cancel_receipt_id',
    ];

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancels', parent::getTable());
    }

    public function invoiceCfdi(): BelongsTo
    {
        return $this->belongsTo(InvoiceCfdi::class);
    }

    public function invoiceDocuments(): MorphMany
    {
        return $this->morphMany(InvoiceDocument::class, 'documentable');
    }
}
