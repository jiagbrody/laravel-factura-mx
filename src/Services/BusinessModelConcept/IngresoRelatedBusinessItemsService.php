<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\BusinessModelConcept;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\Invoice;

class IngresoRelatedBusinessItemsService
{
    protected Builder $queryBuilder;

    protected Builder $concepts;

    public function __construct(protected Invoice $invoice)
    {
        $this->queryBuilder = DB::table(config('jiagbrody-laravel-factura-mx.table_names.invoice_related_concept_pivot'));

        $this->concepts = $this->queryBuilder->where('invoice_id', '=', $invoice->id);
    }

    public function setInvoice(Invoice $invoice): void {}

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function get(): Collection
    {
        return $this->concepts->get();
    }

    public function getBuilder(): Builder
    {
        return $this->concepts;
    }

    public function setConceptsByInsert(array $concepts): void
    {
        $this->queryBuilder->insert($concepts);
    }
}
