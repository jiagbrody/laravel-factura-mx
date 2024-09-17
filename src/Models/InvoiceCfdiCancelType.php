<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCfdiCancelType extends Model
{
    use HasFactory;

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_cfdi_cancel_types', parent::getTable());
    }
}
