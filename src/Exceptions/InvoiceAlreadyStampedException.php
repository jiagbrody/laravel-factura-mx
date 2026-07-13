<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

/**
 * La factura ya tiene un CFDI timbrado; volver a enviarla al PAC podría
 * generar un segundo UUID (doble factura fiscal ante el SAT).
 */
final class InvoiceAlreadyStampedException extends FacturaMxException
{
    public static function withUuid(string $uuid): self
    {
        return new self('Esta factura ya se encuentra timbrada (UUID: '.$uuid.').');
    }
}
