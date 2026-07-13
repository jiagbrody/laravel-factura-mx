<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Rules;

use JiagBrody\LaravelFacturaMx\Enums\InvoicePaymentTypeEnum;

class ComprobanteDeIngresoRuleHelper
{
    public static function getPaymentTypeId(string $metodoPago): int
    {
        return match ($metodoPago) {
            'PUE' => InvoicePaymentTypeEnum::PAGO_EN_UNA_EXHIBICION->value,
            'PPD' => InvoicePaymentTypeEnum::PAGO_A_LINEA_DE_CREDITO->value,
            default => throw new \ValueError('MetodoPago desconocido: "'.$metodoPago.'". Valores válidos: PUE, PPD.'),
        };
    }

    public static function getIsPaid(string $metodoPago): bool
    {
        return match ($metodoPago) {
            'PUE' => true,
            'PPD' => false,
            default => throw new \ValueError('MetodoPago desconocido: "'.$metodoPago.'". Valores válidos: PUE, PPD.'),
        };
    }
}
