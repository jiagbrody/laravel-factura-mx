<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders;

use Illuminate\Support\Facades\DB;

class AllSimpleRelationDataQuery
{
    private \Illuminate\Database\Query\Builder $querySource;

    public function __construct()
    {
        $invoices = config('jiagbrody-laravel-factura-mx.table_names.invoices');
        $invoiceTypes = config('jiagbrody-laravel-factura-mx.table_names.invoice_types');
        $invoiceCompanies = config('jiagbrody-laravel-factura-mx.table_names.invoice_companies');
        $invoiceStatuses = config('jiagbrody-laravel-factura-mx.table_names.invoice_statuses');
        $invoiceBalances = config('jiagbrody-laravel-factura-mx.table_names.invoice_balances');
        $invoicePaymentTypes = config('jiagbrody-laravel-factura-mx.table_names.invoice_payment_types');
        $invoiceCfdis = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdis');
        $invoiceCfdiCancels = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancels');
        $invoiceCfdiCancelTypes = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancel_types');
        $invoiceComplementLocalTaxes = config('jiagbrody-laravel-factura-mx.table_names.invoice_complement_local_taxes');
        $invoiceComplementLocalTaxDetails = config('jiagbrody-laravel-factura-mx.table_names.invoice_complement_local_tax_details');
        $invoiceTaxes = config('jiagbrody-laravel-factura-mx.table_names.invoice_taxes');

        $this->querySource = DB::table($invoices . ' as i')
            // $this->querySource = Invoice::query()
            ->from($invoices . ' as i')
            ->select($this->obtainSelectOfInvoices())
            ->join($invoiceTypes . ' as iT', 'i.invoice_type_id', '=', 'iT.id')
            ->join($invoiceStatuses . ' as iS', 'i.invoice_status_id', '=', 'iS.id')
            ->join($invoiceCompanies . ' as iC', 'i.invoice_company_id', '=', 'iC.id')
            ->join($invoiceBalances . ' as iB', 'i.id', '=', 'iB.invoice_id')
            ->join($invoicePaymentTypes . ' as iPT', 'iB.invoice_payment_type_id', '=', 'iPT.id')
            ->leftJoin($invoiceComplementLocalTaxes . ' as iCLOCALTAXE', 'i.id', '=', 'iCLOCALTAXE.invoice_id')
            ->leftJoin($invoiceComplementLocalTaxDetails, 'iCLOCALTAXE.id', '=', $invoiceComplementLocalTaxDetails . '.invoice_complement_local_tax_id')
            ->leftJoin($invoiceCfdis . ' as iCFDI', 'i.id', '=', 'iCFDI.invoice_id')
            //TODO: CHECAR SI LAS CANCELACIONES LAS DEJO COMO SOLO "hasOne" Y LOS ACUSES CREAR OTRA TABLA SEPARADA DE ACUSES.
            ->leftJoin($invoiceCfdiCancels . ' as iCCANCEL', 'iCFDI.id', '=', 'iCCANCEL.invoice_cfdi_id')
            ->leftJoin($invoiceCfdiCancelTypes . ' as iCCANCELTYPE', 'iCCANCEL.invoice_cfdi_cancel_type_id', '=', 'iCCANCELTYPE.id')
            ->leftJoin($invoiceTaxes, 'i.id', '=', $invoiceTaxes . '.invoice_id');
    }

    private function obtainSelectOfInvoices(): array
    {
        return [
            'i.id',
            'i.id as invoice_id',
            'i.user_id',
            'i.invoiceable_type',
            'i.invoiceable_id',
            'iT.id as invoice_type_id',
            'iT.name as invoice_type_name',
            'iC.id as invoice_company_id',
            'iC.name as invoice_company_name',
            'iC.rfc as invoice_company_rfc',
            'iC.nombre as invoice_company_nombre',
            'iC.domicilio_fiscal as invoice_company_domicilio_fiscal',
            'iC.residencia_fiscal as invoice_company_residencia_fiscal',
            'iC.num_reg_id_trib as invoice_company_num_reg_id_trib',
            'iC.regimen_fiscal as invoice_company_regimen_fiscal',
            'iS.id as invoice_status_id',
            'iS.name as invoice_status_name',
            'iB.is_paid as invoice_balance_is_paid',
            // 'iPT.id as invoice_payment_type_id',
            'iPT.name as invoice_payment_type_name',
            'iCFDI.id as invoice_cfdi_id',
            'iCFDI.user_id as invoice_cfdi_user_id',
            'iCFDI.uuid as invoice_cfdi_uuid',
            'iCCANCEL.id as invoice_cfdi_cancel_id',
            'iCCANCELTYPE.id as invoice_cfdi_cancel_type_id',
            'iCCANCELTYPE.name as invoice_cfdi_cancel_type_name',
            'iCLOCALTAXE.id as invoice_complement_local_tax_id',
            'iCLOCALTAXE.total_de_retenciones as invoice_complement_local_tax_total_de_retenciones',
            'iCLOCALTAXE.total_de_traslados as invoice_complement_local_tax_total_de_traslados',
            'i.created_at as invoice_created_at',
        ];
    }

    public function getQuerySource()
    {
        return $this->querySource;
    }
}
