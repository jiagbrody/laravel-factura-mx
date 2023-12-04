<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Enums;

use App\Models\InvoicePaymentType;
use Illuminate\Support\Facades\DB;

enum InvoicePaymentTypeEnum: int
{
    case PAGO_EN_UNA_EXHIBICION  = 1;
    case PAGO_A_LINEA_DE_CREDITO = 2;

    public static function getCatalog(): \Illuminate\Support\Collection
    {
        return DB::table((new InvoicePaymentType)->getTable())->get(['id', 'name']);
    }

    public function getName(): string
    {
        return match ($this) {
            self::PAGO_EN_UNA_EXHIBICION => 'Pago en una exhibición',
            self::PAGO_A_LINEA_DE_CREDITO => 'Pago a linea de credito',
        };
    }
}
