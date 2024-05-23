<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Helpers\AddReadableDatesHelperTrait;

class Invoice extends Model
{
    use AddReadableDatesHelperTrait, HasFactory;

    protected $appends = [
        'created_at_format',
        'created_at_human',
    ];

    public function invoiceCfdi(): HasOne
    {
        return $this->hasOne(InvoiceCfdi::class);
    }

    public function invoiceBalance(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InvoiceBalance::class);
    }

    public function invoiceDetail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InvoiceDetail::class);
    }

    public function invoiceTaxes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceTax::class);
    }

    public function invoiceTax(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InvoiceTax::class);
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

    public function documents(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(InvoiceDocument::class, 'documentable');
    }

    public function pdfInvoiceDocument(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(InvoiceDocument::class, 'documentable')->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->where('document_type_id', '=', InvoiceDocumentTypeEnum::PDF_FILE->value);
        });
    }

    public function xmlInvoiceDocument(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(InvoiceDocument::class, 'documentable')->ofMany([
            'created_at' => 'max',
            'id' => 'max',
        ], function ($query) {
            $query->where('document_type_id', '=', InvoiceDocumentTypeEnum::XML_FILE->value);
        });
    }
}
