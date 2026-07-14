<?php

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JiagBrody\LaravelFacturaMx\Helpers\AddReadableDatesHelperTrait;

/**
 * @property int $id
 * @property int $origin_invoice_id
 * @property int $related_invoice_id
 * @property int $invoice_relationship_type_id
 * @property Carbon|null $relationship_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
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
