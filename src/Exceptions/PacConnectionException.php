<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

use Throwable;

/**
 * Fallo de transporte con el PAC (red, WSDL inaccesible, timeout, SoapFault).
 * Es un error REINTENTABLE: el CFDI no fue procesado por el PAC, por lo que
 * un job de cola puede volver a intentar sin riesgo de duplicar el timbrado.
 */
final class PacConnectionException extends FacturaMxException
{
    public static function whileCreatingClient(string $wsdlUrl, Throwable $previous): self
    {
        return new self('No se pudo conectar con el servicio SOAP del PAC ('.$wsdlUrl.'): '.$previous->getMessage(), 0, $previous);
    }

    public static function whileCalling(string $operation, Throwable $previous): self
    {
        return new self('Fallo de comunicación con el PAC durante la operación "'.$operation.'": '.$previous->getMessage(), 0, $previous);
    }
}
