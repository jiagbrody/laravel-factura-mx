<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceRelatedConcept extends Model
{
    use HasFactory;

    public function getTable()
    {
        // return config('jiagbrody-laravel-factura-mx.table_names', parent::getTable());
    }
}
