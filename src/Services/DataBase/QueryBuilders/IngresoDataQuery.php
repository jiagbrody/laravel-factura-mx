<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;

final class IngresoDataQuery extends SimpleRelationDataQuery
{
    use HelpersTrait;

    public function __construct(protected ?Invoice $invoice = null)
    {
        parent::__construct();

        $this->querySource()->addSelect([

        ]);

        $this->querySource()

            ->where('invoices.invoice_type_id', '=', InvoiceTypeEnum::INGRESO->value);
    }

    public function getByInvoice(): \stdClass
    {
        $this->checkLogicalErrorTrait();

        return $this->querySource()->where('invoices.id', '=', $this->invoice->id)->first();
    }

    public function getQueryBuilder()
    {
        return $this->querySource();
    }
}
