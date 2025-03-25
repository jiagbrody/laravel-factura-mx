<?php

namespace JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Models\Invoice;

class RelacionesDataQuery extends SimpleRelationDataQuery
{
    use HelpersTrait;

    protected $origin;

    protected $related;

    public function __construct(protected ?Invoice $invoice = null)
    {
        parent::__construct();

        $invoiceRelationshipTypes = config('jiagbrody-laravel-factura-mx.table_names.invoice_relationship_types');
        $invoiceRelationships = config('jiagbrody-laravel-factura-mx.table_names.invoice_relationships');

        $this->querySource()->addSelect([
            'invoice_relationships.id as invoice_relationship_id',
            'invoice_relationships.origin_invoice_id as invoice_relationship_origin_invoice_id',
            'invoice_relationships.related_invoice_id as invoice_relationship_related_invoice_id',
            'invoice_relationships.relationship_date as invoice_relationship_relationship_date',
            'invoice_relationship_types.id as invoice_relationship_type_id',
            'invoice_relationship_types.name as invoice_relationship_type_name',
        ]);

        $this->querySource()
            ->leftJoin($invoiceRelationships.' as invoice_relationships', 'invoices.id', '=', 'invoice_relationships.origin_invoice_id')
            ->leftJoin($invoiceRelationshipTypes.' as invoice_relationship_types', 'invoice_relationships.invoice_relationship_type_id', '=', 'invoice_relationship_types.id');
    }

    public function getOriginAndRelated(): Collection
    {
        $this->checkLogicalErrorTrait();

        $clone = clone $this->querySource();
        $clone2 = clone $this->querySource();

        return collect([
            'origin' => $clone2->where('iR.origin_invoice_id', $this->invoice->id)->get(),
            'related' => $clone->where('iR.related_invoice_id', $this->invoice->id)->get(),
            'test' => $this->querySource()->get(),
        ]);
    }

    public function getQueryBuilder()
    {
        return $this->querySource();
    }
}
