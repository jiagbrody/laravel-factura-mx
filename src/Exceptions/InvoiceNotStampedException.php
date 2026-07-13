<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

/**
 * La operación requiere una factura timbrada (con CFDI/UUID) y la factura
 * no lo tiene: consultar estatus, cancelar o recuperar el XML timbrado.
 */
final class InvoiceNotStampedException extends FacturaMxException
{
    public static function forOperation(string $operation): self
    {
        return new self('La factura no tiene CFDI timbrado; no es posible ejecutar la operación "'.$operation.'".');
    }
}
