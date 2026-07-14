<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Tests\Support;

use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacSoapCallerInterface;
use RuntimeException;
use stdClass;

/**
 * Doble de prueba del transporte SOAP: entrega respuestas grabadas en orden
 * y registra cada llamada (operación + parámetros) para las aserciones.
 */
final class FakeSoapCaller implements PacSoapCallerInterface
{
    /** @var array<int, stdClass> */
    private array $queuedResponses;

    /** @var array<int, array{operation: string, params: array}> */
    public array $calls = [];

    public function __construct(stdClass ...$responses)
    {
        $this->queuedResponses = $responses;
    }

    public function call(string $wsdlUrl, string $operation, array $params, string $logName): stdClass
    {
        $this->calls[] = ['operation' => $operation, 'params' => $params];

        $response = array_shift($this->queuedResponses);

        if ($response === null) {
            throw new RuntimeException('FakeSoapCaller: no hay más respuestas encoladas (operación solicitada: "'.$operation.'").');
        }

        return $response;
    }

    /**
     * @return array<int, string>
     */
    public function operations(): array
    {
        return array_column($this->calls, 'operation');
    }
}
