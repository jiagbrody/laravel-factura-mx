<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Exceptions;

use CfdiUtils\Validate\Asserts;

/**
 * El CFDI no pasó la validación local (XSD + reglas del SAT vía cfdiutils)
 * antes de enviarse al PAC. Corregir localmente evita quemar intentos de
 * timbrado y ensuciar invoice_incidents con errores prevenibles.
 */
final class CfdiPreValidationException extends FacturaMxException
{
    /**
     * @param  array<int, string>  $errors
     */
    private function __construct(string $message, public readonly array $errors)
    {
        parent::__construct($message);
    }

    public static function fromAsserts(Asserts $asserts): self
    {
        $errors = [];
        foreach ($asserts->errors() as $error) {
            $errors[] = '['.$error->getCode().'] '.$error->getTitle().': '.$error->getExplanation();
        }

        return new self(
            "El CFDI no pasó la validación local; no se envió al PAC. Errores:\n".implode("\n", $errors),
            $errors
        );
    }
}
