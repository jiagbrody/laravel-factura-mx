<?php

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InvoiceCfdiCancelReceipt extends Model
{
    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancel_receipts', parent::getTable());
    }

    protected $fillable = [
        'invoice_cfdi_cancel_type_id',
        'replacement_invoice_cfdi_id',
        'receipt_date',
    ];

    public function invoiceCfdiCancelType(): BelongsTo
    {
        return $this->belongsTo(InvoiceCfdiCancelType::class);
    }

    public function replacementInvoiceCfdi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceCfdi::class, 'replacement_invoice_cfdi_id');
    }

    public function invoiceCfdi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceCfdi::class);
    }

    public function invoiceDocuments(): MorphMany
    {
        return $this->morphMany(InvoiceDocument::class, 'documentable');
    }
}
