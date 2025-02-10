<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Helpers\AddReadableDatesHelperTrait;

class Invoice extends Model
{
    use AddReadableDatesHelperTrait, HasFactory;

    protected $appends = [
        'created_at_format',
        'created_at_human',
    ];

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoices', parent::getTable());
    }

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function invoiceCfdi(): HasOne
    {
        return $this->hasOne(InvoiceCfdi::class);
    }

    public function invoiceBalance(): HasOne
    {
        return $this->hasOne(InvoiceBalance::class);
    }

    public function invoiceDetail(): HasOne
    {
        return $this->hasOne(InvoiceDetail::class);
    }

    public function invoiceTax(): HasOne
    {
        return $this->hasOne(InvoiceTax::class);
    }

    public function invoiceComplementLocalTax(): HasOne
    {
        return $this->hasOne(InvoiceComplementLocalTax::class);
    }

    public function invoiceType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceType::class);
    }

    public function invoiceCompany(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceCompany::class);
    }

    public function invoiceStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceStatus::class);
    }

    public function invoiceDocuments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(InvoiceDocument::class, 'documentable');
    }

    public function invoiceIncidents(): HasMany
    {
        return $this->hasMany(InvoiceIncident::class);
    }

    // public function documents(): \Illuminate\Database\Eloquent\Relations\MorphMany
    // {
    //     return $this->morphMany(InvoiceDocument::class, 'documentable');
    // }

    public function pdfInvoiceDocument(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(InvoiceDocument::class, 'documentable')->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->where('invoice_document_type_id', '=', InvoiceDocumentTypeEnum::PDF_FILE->value);
        });
    }

    public function xmlInvoiceDocument(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(InvoiceDocument::class, 'documentable')->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->where('invoice_document_type_id', '=', InvoiceDocumentTypeEnum::XML_FILE->value);
        });
    }
}
