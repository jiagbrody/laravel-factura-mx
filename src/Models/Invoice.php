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

    /*
     * UPDATE 2024-11-28: SE QUITO ESTA TABLA Y SE RESOLVIO MEJOR UN SERVICIO.
     * TRATAR EL MANEJO DE REGISTROS DE UN MODELO QUE NO EXISTE AQUI POR SER MODELO DE NEGOCIO DEL CLIENTE
     * ERA IMPOSIBLE ESTE TEMA EN ESTA LIBRERIA CON RELACIONES NORMALES DE LARAVEL "Many to Many".
     * REFERENTE A GUARDAR Y OBTENER LOS REGISTROS POR MEDIO DE "ELOQUENT".
     *
     * HAY DOS MANERAS:
     *  -- SE RESOLVIO HACIENDO UNA CLASE DE SERVICIO SIN ELOQUENT LLAMADA "BusinessModelConceptService".
     *
     *  -- OTRA SOLUCION ES QUE EL CLIENTE HAGA UN MODELO "Invoice" EN SU PROYECTO Y LAS RELACIONES
     *     "Many to Many" CON RELACION A SUS CONCEPTOS DE SU MODELO DE NEGOCIO.
     */
    // public function invoiceRelatedConcepts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    // {
    //     $tablePivot = config('jiagbrody-laravel-factura-mx.table_names.invoice_related_concept_pivot');
    //     $columnRelated = config('jiagbrody-laravel-factura-mx.column_names.foreign_id_related_to_concepts');
    //
    //     return $this->belongsToMany(??????????, $tablePivot, 'invoice_id', $columnRelated);
    // }

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
