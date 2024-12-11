<?php

namespace JiagBrody\LaravelFacturaMx\Services\DataBase;

use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders\AllSimpleRelationDataQuery;

class DatabaseService
{
    private AllSimpleRelationDataQuery $allSimpleRelationDataQuery;

    public function __construct(protected Invoice $invoice)
    {
        $this->allSimpleRelationDataQuery = new AllSimpleRelationDataQuery;
    }

    public function getAllSimpleRelationData(): null|object
    {
        return $this->allSimpleRelationDataQuery->getQuerySource()->where('i.id', '=', $this->invoice->id)->first();
    }

    public function getIncidents(): Collection
    {
        return $this->invoice->invoiceIncidents;
    }
}
