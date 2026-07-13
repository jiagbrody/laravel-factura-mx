<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

/**
 * El PAC respondió, pero con una estructura que el paquete no reconoce.
 * NO es reintentable a ciegas: hay que revisar el log SOAP correspondiente
 * en storage/logs para entender qué devolvió realmente el servicio.
 */
final class PacUnexpectedResponseException extends FacturaMxException
{
    public static function missingNode(string $operation, string $node): self
    {
        return new self('La respuesta del PAC a la operación "'.$operation.'" no contiene el nodo esperado "'.$node.'". Revisa el log SOAP en storage/logs.');
    }

    public static function withStatus(string $operation, string $codEstatus): self
    {
        return new self('La respuesta del PAC a la operación "'.$operation.'" no es procesable. CodEstatus: '.$codEstatus.'. Revisa el log SOAP en storage/logs.');
    }

    public static function notAnObject(string $operation): self
    {
        return new self('La respuesta del PAC a la operación "'.$operation.'" no es un objeto SOAP válido. Revisa el log SOAP en storage/logs.');
    }
}
