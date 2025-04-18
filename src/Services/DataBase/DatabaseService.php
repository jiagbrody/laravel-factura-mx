<?php

namespace JiagBrody\LaravelFacturaMx\Services\DataBase;

use Illuminate\Database\Query\Builder;
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
