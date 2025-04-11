<?php

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JiagBrody\LaravelFacturaMx\Helpers\AddReadableDatesHelperTrait;

class InvoiceRelationship extends Model
{
    use AddReadableDatesHelperTrait, HasFactory;

    protected $fillable = [
        'origin_invoice_id',
        'related_invoice_id',
        'invoice_relationship_type_id',
        'relationship_date',
    ];

    protected $appends = [
        'created_at_format',
        'created_at_human',
    ];

    public function getTable()
    {
        return config('jiagbrody-laravel-factura-mx.table_names.invoice_relationships', parent::getTable());
    }
}
