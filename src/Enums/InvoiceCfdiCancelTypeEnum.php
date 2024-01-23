<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Enums;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdiCancelType;

enum InvoiceCfdiCancelTypeEnum: int
{
    case NEW_WITH_ERRORS_RELATED = 1;

    case NEW_WITH_ERRORS_UNRELATED = 2;

    case NEW_NOT_EXECUTED = 3;

    case NEW_NORMATIVE_TO_GLOBAL = 4;

    public static function getCatalog(): \Illuminate\Support\Collection
    {
        return DB::table((new InvoiceCfdiCancelType)->getTable())->get(['id', 'name', 'description']);
    }

    public function getSatId(): string
    {
        return match ($this) {
            self::NEW_WITH_ERRORS_RELATED => '01',
            self::NEW_WITH_ERRORS_UNRELATED => '02',
            self::NEW_NOT_EXECUTED => '03',
            self::NEW_NORMATIVE_TO_GLOBAL => '04',
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::NEW_WITH_ERRORS_RELATED => 'Comprobantes emitidos con errores con relación',
            self::NEW_WITH_ERRORS_UNRELATED => 'Comprobantes emitidos con errores sin relación',
            self::NEW_NOT_EXECUTED => 'No se llevó a cabo la operación',
            self::NEW_NORMATIVE_TO_GLOBAL => 'Operación nominativa relacionada en una factura global',
        };
    }
}
