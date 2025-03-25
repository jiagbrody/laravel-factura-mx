<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders;

use Illuminate\Support\Facades\DB;

class SimpleRelationDataQuery
{
    private \Illuminate\Database\Query\Builder $querySource;

    public function __construct()
    {
        $invoices = config('jiagbrody-laravel-factura-mx.table_names.invoices');
        $invoiceTypes = config('jiagbrody-laravel-factura-mx.table_names.invoice_types');
        $invoiceCompanies = config('jiagbrody-laravel-factura-mx.table_names.invoice_companies');
        $invoiceStatuses = config('jiagbrody-laravel-factura-mx.table_names.invoice_statuses');
        $invoiceCfdis = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdis');
        $invoiceCfdiCancels = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancels');
        $invoiceCfdiCancelTypes = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancel_types');

        $this->querySource = DB::table($invoices.'invoices')
            // $this->querySource = Invoice::query()
            ->from($invoices.' as invoices')
            ->select($this->obtainSelectOfInvoices())
            ->join($invoiceTypes.' as invoice_types', 'invoices.invoice_type_id', '=', 'invoice_types.id')
            ->join($invoiceStatuses.' as invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
            ->join($invoiceCompanies.' as invoice_companies', 'invoices.invoice_company_id', '=', 'invoice_companies.id')
            ->leftJoin($invoiceCfdis.' as invoice_cfdis', 'invoices.id', '=', 'invoice_cfdis.invoice_id')
            // TODO: CHECAR SI LAS CANCELACIONES LAS DEJO COMO SOLO "hasOne" Y LOS ACUSES CREAR OTRA TABLA SEPARADA DE ACUSES.
            ->leftJoin($invoiceCfdiCancels.' as invoice_cfdi_cancels', 'invoice_cfdis.id', '=', 'invoice_cfdi_cancels.invoice_cfdi_id')
            ->leftJoin($invoiceCfdiCancelTypes.' as invoice_cfdi_cancel_types', 'invoice_cfdi_cancels.invoice_cfdi_cancel_type_id', '=', 'invoice_cfdi_cancel_types.id');
    }

    private function obtainSelectOfInvoices(): array
    {
        return [
            'invoices.id',
            'invoices.id as invoice_id',
            'invoices.user_id',
            'invoice_types.id as invoice_type_id',
            'invoice_types.name as invoice_type_name',
            'invoice_companies.id as invoice_company_id',
            'invoice_companies.name as invoice_company_name',
            'invoice_companies.rfc as invoice_company_rfc',
            'invoice_companies.nombre as invoice_company_nombre',
            'invoice_companies.domicilio_fiscal as invoice_company_domicilio_fiscal',
            'invoice_companies.residencia_fiscal as invoice_company_residencia_fiscal',
            'invoice_companies.num_reg_id_trib as invoice_company_num_reg_id_trib',
            'invoice_companies.regimen_fiscal as invoice_company_regimen_fiscal',
            'invoice_statuses.id as invoice_status_id',
            'invoice_statuses.name as invoice_status_name',
            // DB::raw('DATE_FORMAT(invoice_details.fecha, "%d/%c/%Y %r") as invoice_detail_fecha_format'),
            'invoice_cfdis.id as invoice_cfdi_id',
            'invoice_cfdis.user_id as invoice_cfdi_user_id',
            'invoice_cfdis.uuid as invoice_cfdi_uuid',
            'invoice_cfdi_cancels.id as invoice_cfdi_cancel_id',
            'invoice_cfdi_cancel_types.id as invoice_cfdi_cancel_type_id',
            'invoice_cfdi_cancel_types.name as invoice_cfdi_cancel_type_name',
            'invoices.created_at as invoice_created_at',
        ];
    }

    protected function querySource()
    {
        return $this->querySource;
    }
}
