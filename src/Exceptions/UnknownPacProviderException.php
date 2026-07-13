<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

final class UnknownPacProviderException extends FacturaMxException
{
    public static function for(string $chosen): self
    {
        return new self('El proveedor PAC "'.$chosen.'" (config "pac_chosen") no está implementado. Proveedores disponibles: finkok.');
    }
}
