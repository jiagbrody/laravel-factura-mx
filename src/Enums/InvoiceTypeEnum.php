<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Enums;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\InvoiceType;

enum InvoiceTypeEnum: int
{
    case INGRESO  = 1;
    case EGRESO   = 2;
    case TRASLADO = 3;
    case NOMINA   = 4;
    case PAGO     = 5;

    public static function getCatalog(): \Illuminate\Support\Collection
    {
        return DB::table((new InvoiceType)->getTable())->get(['id', 'name']);
    }

    public function getName(): string
    {
        return match ($this) {
            self::INGRESO => 'Ingreso',
            self::EGRESO => 'Egreso',
            self::TRASLADO => 'Traslado',
            self::NOMINA => 'Nomina',
            self::PAGO => 'Pago',
        };
    }
}
