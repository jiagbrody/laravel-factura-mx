<?php

namespace JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders;

use JiagBrody\LaravelFacturaMx\Models\Invoice;

final class IngresoDataQuery extends AllSimpleRelationDataQuery
{
    public function __invoke(Invoice $invoice): \stdClass
    {
        $invoicePaymentTypes = config('jiagbrody-laravel-factura-mx.table_names.invoice_payment_types');
        $invoiceComplementLocalTaxes = config('jiagbrody-laravel-factura-mx.table_names.invoice_complement_local_taxes');
        $invoiceComplementLocalTaxDetails = config('jiagbrody-laravel-factura-mx.table_names.invoice_complement_local_tax_details');

        $this->querySource()->addSelect([
            'iPT.id as invoice_payment_type_id',
            'iPT.name as invoice_payment_type_name',
            'iCLOCALTAXE.id as invoice_complement_local_tax_id',
            'iCLOCALTAXE.total_de_retenciones as invoice_complement_local_tax_total_de_retenciones',
            'iCLOCALTAXE.total_de_traslados as invoice_complement_local_tax_total_de_traslados',
        ]);

        $this->querySource()
            ->leftJoin($invoicePaymentTypes . ' as iPT', 'iB.invoice_payment_type_id', '=', 'iPT.id')
            ->leftJoin($invoiceComplementLocalTaxes . ' as iCLOCALTAXE', 'i.id', '=', 'iCLOCALTAXE.invoice_id')
            ->leftJoin($invoiceComplementLocalTaxDetails, 'iCLOCALTAXE.id', '=', $invoiceComplementLocalTaxDetails . '.invoice_complement_local_tax_id');

        return $this->querySource()->where('i.id', '=', $invoice->id)->first();
    }
}
