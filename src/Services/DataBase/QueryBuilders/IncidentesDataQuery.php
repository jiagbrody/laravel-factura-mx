<?php

namespace JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceIncident;

class IncidentesDataQuery extends SimpleRelationDataQuery
{
    use HelpersTrait;

    public function __construct(protected Invoice $invoice)
    {
        parent::__construct();
    }

    public function getByInvoice(): \Illuminate\Support\Collection
    {
        $this->checkLogicalErrorTrait();

        // return $this->querySource()->where('i.id', '=', $this->invoice->id)->first();
        // return $this->invoice->invoiceIncidents;
        return DB::table((new InvoiceIncident)->getTable())->where('invoice_id', $this->invoice->id)->get();
    }

    public function getQueryBuilder()
    {
        return $this->querySource();
    }
}
