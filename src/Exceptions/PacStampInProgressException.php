<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

/**
 * El PAC recibió el comprobante y sigue procesándolo en su cola asíncrona
 * ("Comprobante recibido satisfactoriamente" sin XML). Reintentar el timbrado
 * más tarde es seguro: si ya quedó timbrado, el paquete recupera el resultado
 * original en lugar de generar un duplicado.
 */
final class PacStampInProgressException extends FacturaMxException
{
    public static function afterAttempts(int $attempts): self
    {
        return new self('El PAC recibió el comprobante pero sigue procesándolo (timbrado asíncrono); no hubo resultado final tras '.$attempts.' consultas a "stamped". Reintenta el timbrado en unos segundos.');
    }
}
