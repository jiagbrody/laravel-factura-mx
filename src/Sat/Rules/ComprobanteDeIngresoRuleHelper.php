<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Rules;

use JiagBrody\LaravelFacturaMx\Enums\InvoicePaymentTypeEnum;

class ComprobanteDeIngresoRuleHelper
{
    public static function getPaymentTypeId(string $metodoPago): int
    {
        if (($metodoPago === 'PUE')) {
            return InvoicePaymentTypeEnum::PAGO_EN_UNA_EXHIBICION->value;
        } elseif (($metodoPago === 'PPD')) {
            return InvoicePaymentTypeEnum::PAGO_A_LINEA_DE_CREDITO->value;
        }
    }

    public static function getIsPaid(string $metodoPago): bool
    {
        if (($metodoPago === 'PUE')) {
            return true;
        } elseif (($metodoPago === 'PPD')) {
            return false;
        }
    }
}
