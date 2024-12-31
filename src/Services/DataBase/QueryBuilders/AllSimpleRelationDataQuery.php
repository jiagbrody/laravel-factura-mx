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
        $invoiceDetails = config('jiagbrody-laravel-factura-mx.table_names.invoice_details');
        $invoiceCfdis = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdis');
        $invoiceCfdiCancels = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancels');
        $invoiceCfdiCancelTypes = config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancel_types');
        $invoiceTaxes = config('jiagbrody-laravel-factura-mx.table_names.invoice_taxes');

        $this->querySource = DB::table($invoices.' as i')
            // $this->querySource = Invoice::query()
            ->from($invoices.' as i')
            ->select($this->obtainSelectOfInvoices())
            ->join($invoiceTypes . ' as iT', 'i.invoice_type_id', '=', 'iT.id')
            ->join($invoiceStatuses . ' as iS', 'i.invoice_status_id', '=', 'iS.id')
            ->join($invoiceCompanies . ' as iC', 'i.invoice_company_id', '=', 'iC.id')
            ->join($invoiceBalances . ' as iB', 'i.id', '=', 'iB.invoice_id')
            ->leftJoin($invoiceDetails . ' as iD', 'i.id', '=', 'iD.invoice_id')
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
            'iD.version as invoice_detail_version',
            'iD.folio as invoice_detail_folio',
            'iD.serie as invoice_detail_serie',
            'iD.fecha as invoice_detail_fecha',
            DB::raw('DATE_FORMAT(iD.fecha, "%d/%c/%Y %r") as invoice_detail_fecha_format'),
            'iD.receptor_rfc as invoice_detail_receptor_rfc',
            'iD.forma_pago as invoice_detail_forma_pago',
            'iD.moneda as invoice_detail_moneda',
            'iD.metodo_pago as invoice_detail_metodo_pago',
            'iD.total as invoice_detail_total',
            'iD.tipo_de_comprobante as invoice_detail_tipo_de_comprobante',
            'iB.is_paid as invoice_balance_is_paid',
            'iCFDI.id as invoice_cfdi_id',
            'iCFDI.user_id as invoice_cfdi_user_id',
            'iCFDI.uuid as invoice_cfdi_uuid',
            'iCCANCEL.id as invoice_cfdi_cancel_id',
            'iCCANCELTYPE.id as invoice_cfdi_cancel_type_id',
            'iCCANCELTYPE.name as invoice_cfdi_cancel_type_name',
            'i.created_at as invoice_created_at',
        ];
    }

    public function querySource()
    {
        return $this->querySource;
    }
}
