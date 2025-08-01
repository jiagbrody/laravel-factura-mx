<?php

namespace JiagBrody\LaravelFacturaMx\Services\DataBase;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders\IncidentesDataQuery;
use JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders\IngresoDataQuery;
use JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders\RelacionesDataQuery;
use JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders\SimpleRelationDataQuery;

class DatabaseService extends SimpleRelationDataQuery
{
    protected Invoice $invoice;

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getInvoiceInformationCollect(): \Illuminate\Support\Collection
    {
        return collect([
            'id' => $this->invoice->id,
            'invoice_id' => $this->invoice->id,
            'invoice_version' => $this->invoice->version,
            'invoice_date' => $this->invoice->invoice_date,
            'invoice_date_format' => $this->invoice->invoice_date->format('d-m-Y H:i:s a'),
            'invoice_date_human' => $this->invoice->invoice_date->diffForHumans(),
            'serie' => $this->invoice->serie,
            'folio' => $this->invoice->folio,
            'invoice_type_id' => $this->invoice->invoiceType->id,
            'invoice_type_name' => $this->invoice->invoiceType->name,
            'invoice_company_id' => $this->invoice->invoiceCompany->id,
            'invoice_company_name' => $this->invoice->invoiceCompany->name,
            'invoice_status_id' => $this->invoice->invoiceStatus->id,
            'invoice_status_name' => $this->invoice->invoiceStatus->name,
            'invoice_cfdi_id' => $this->invoice?->invoiceCfdi?->id,
            'invoice_cfdi_uuid' => $this->invoice?->invoiceCfdi?->uuid,
            'origin_relationships' => $this->invoice->originInvoiceRelationships,
            'related_relationships' => $this->invoice->relatedInvoiceRelationships,
        ]);
    }

    public function getIncidents(): Collection
    {
        return $this->invoice->invoiceIncidents()->get()->collect();
    }

    public function getAllSimpleRelationQueryBuilder(): Builder
    {
        return $this->querySource();
    }

    public function getInfoDataBuilder()
    {
        return $this->querySource();
    }

    public function getInfoDataByInvoice()
    {
        return $this->querySource()->where('invoices.id', '=', $this->invoice->id)->first();
    }

    public function chooseIngresoData(): object
    {
        return new IngresoDataQuery($this->invoice ?? null);
    }

    public function getIncidentesData(): object
    {
        return new IncidentesDataQuery($this->invoice);
    }

    public function chooseAllRelationshipsPerInvoice(): object
    {
        return new RelacionesDataQuery($this->invoice ?? null);
    }
}
