<?php

namespace JiagBrody\LaravelFacturaMx\Helpers;

use Illuminate\Support\Facades\DB;

class LaravelFacturaMxCatalogsTablesHelper
{
    public function getInvoiceCompany(int $id)
    {
        return DB::table(config('laravel-factura-mx.tables.invoices.invoice'))->where('id', $id)->first();
    }
}
