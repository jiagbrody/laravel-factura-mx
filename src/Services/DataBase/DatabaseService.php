<?php

namespace JiagBrody\LaravelFacturaMx\Services\DataBase;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders\AllSimpleRelationDataQuery;
use JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders\IngresoDataQuery;

class DatabaseService extends AllSimpleRelationDataQuery
{
    protected Invoice $invoice;

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function getAllSimpleRelationData(): Builder
    {
        return $this->querySource();
    }

    public function getIngresoData(): object
    {
        $this->checkLogicalError();

        return (new IngresoDataQuery())($this->invoice);
    }

    public function getIncidents(): Collection
    {
        $this->checkLogicalError();
        
        return $this->invoice->invoiceIncidents;
    }

    private function checkLogicalError(): void
    {
        if (!property_exists($this, "invoice")) {
            abort(422, 'NO ESTA DEFINIDA LA PROPIEDAD "invoice" en la clase "DatabaseService"');
        }
    }
}
