<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JiagBrody\LaravelFacturaMx\Helpers\AddReadableDatesHelperTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $sat_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InvoiceRelationshipType extends Model
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
