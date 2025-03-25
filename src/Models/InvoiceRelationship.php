<?php

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use JiagBrody\LaravelFacturaMx\Helpers\AddReadableDatesHelperTrait;

class InvoiceRelationship extends Model
{
    use AddReadableDatesHelperTrait, HasFactory;

    protected $appends = [
        'created_at_format',
        'created_at_human',
    ];

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_relationship_types', parent::getTable());
    }

}
