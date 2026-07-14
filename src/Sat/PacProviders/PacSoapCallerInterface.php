<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

use stdClass;

/**
 * Transporte SOAP hacia el PAC. La implementación real es
 * Finkok\FinkokSoapCaller; los tests de contrato inyectan un doble con
 * respuestas grabadas para fijar el comportamiento ante cada variante del PAC.
 */
interface PacSoapCallerInterface
{
    public function call(string $wsdlUrl, string $operation, array $params, string $logName): stdClass;
}
