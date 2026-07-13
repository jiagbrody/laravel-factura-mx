<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Enums;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\InvoiceType;

enum InvoiceTypeEnum: int
{
    case INGRESO = 1;
    case EGRESO = 2;
    case TRASLADO = 3;
    case NOMINA = 4;
    case PAGO = 5;

    public static function getCatalog(): Collection
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

    public function getSatCode(): string
    {
        return match ($this) {
            self::INGRESO => 'I',
            self::EGRESO => 'E',
            self::TRASLADO => 'T',
            self::NOMINA => 'N',
            self::PAGO => 'P',
        };
    }

    public static function getFirstBySatCode(string $satCode): InvoiceTypeEnum
    {
        return match ($satCode) {
            'I' => self::INGRESO,
            'E' => self::EGRESO,
            'T' => self::TRASLADO,
            'N' => self::NOMINA,
            'P' => self::PAGO,
            default => throw new \ValueError('TipoDeComprobante desconocido: "'.$satCode.'". Valores válidos: I, E, T, N, P.'),
        };
    }
}
