<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class InvoiceCfdi extends Model
{
    use HasFactory;

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdis', parent::getTable());
    }

    public function invoiceCfdiCancel(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InvoiceCfdiCancel::class);
    }

    public function invoiceCfdiCancelReceipts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(invoiceCfdiCancelReceipt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function invoiceCfdiCancelType(): BelongsTo
    {
        return $this->belongsTo(InvoiceCfdiCancelType::class);
    }

    public function invoiceCfdiType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceCfdiType::class);
    }

    public function invoiceDocuments(): MorphMany
    {
        return $this->morphMany(InvoiceDocument::class, 'documentable');
    }

    public function xmlInvoiceDocument(): MorphOne
    {
        return $this->morphOne(InvoiceDocument::class, 'documentable')->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->where('extension', '=', 'xml');
        });
    }

    public function pdfInvoiceDocument(): MorphOne
    {
        return $this->morphOne(InvoiceDocument::class, 'documentable')->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->where('extension', '=', 'pdf');
        });
    }
}
