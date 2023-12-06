<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Enums;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\InvoiceStatus;
use JiagBrody\LaravelFacturaMx\Models\InvoiceTaxType;

enum InvoiceTaxTypeEnum: int
{
    case TRASLADO    = 1;
    case RETENCION   = 2;

    public static function getCatalog(): \Illuminate\Support\Collection
    {
        return DB::table((new InvoiceTaxType)->getTable())->get(['id', 'name']);
    }

    public function getName(): string
    {
        return match ($this) {
            self::TRASLADO => 'Traslado',
            self::RETENCION => 'Retención',
        };
    }
}
