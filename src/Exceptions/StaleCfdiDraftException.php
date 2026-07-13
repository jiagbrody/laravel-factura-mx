<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

/**
 * El borrador del CFDI quedó fuera de la ventana de 72 horas del SAT
 * (la Fecha del comprobante es demasiado antigua). Enviarlo al PAC
 * garantizaría un rechazo por fecha fuera de rango: hay que regenerar el
 * CFDI con Fecha y sello nuevos antes de timbrar.
 */
final class StaleCfdiDraftException extends FacturaMxException
{
    public static function forFecha(string $fecha, int $maxAgeHours): self
    {
        return new self('El borrador del CFDI tiene Fecha '.$fecha.', más antigua que el límite configurado de '.$maxAgeHours.' horas (ventana SAT: 72 h). Regenera el CFDI (nueva Fecha y nuevo sello) antes de timbrar.');
    }
}
