<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Enums;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\InvoiceStatus;

enum InvoiceStatusEnum: int
{
    case DRAFT = 1;
    case VIGENT = 2;
    case CANCELED = 3;
    case PRIOR_CANCELLATION = 4;

    public static function getCatalog(): \Illuminate\Support\Collection
    {
        return DB::table((new InvoiceStatus)->getTable())->get(['id', 'name']);
    }

    public function getName(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::VIGENT => 'Vigente',
            self::CANCELED => 'Cancelado',
            self::PRIOR_CANCELLATION => 'Pre cancelación',
        };
    }
}
