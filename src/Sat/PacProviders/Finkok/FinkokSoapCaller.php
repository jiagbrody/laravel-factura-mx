<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use Illuminate\Support\Facades\Log;
use JiagBrody\LaravelFacturaMx\Exceptions\PacConnectionException;
use JiagBrody\LaravelFacturaMx\Exceptions\PacUnexpectedResponseException;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacSoapCallerInterface;
use SoapClient;
use SoapFault;
use stdClass;

/**
 * Punto único de comunicación SOAP con Finkok: timeouts, registro del
 * request/response (también cuando la llamada falla) y redacción de la
 * contraseña del PAC antes de escribir al log.
 */
final class FinkokSoapCaller implements PacSoapCallerInterface
{
    public function __construct(private readonly int $timeoutSeconds) {}

    public function call(string $wsdlUrl, string $operation, array $params, string $logName): stdClass
    {
        try {
            $client = new SoapClient($wsdlUrl, [
                'trace' => 1,
                'exceptions' => true,
                'connection_timeout' => $this->timeoutSeconds,
                'cache_wsdl' => WSDL_CACHE_BOTH,
                'stream_context' => stream_context_create([
                    'http' => ['timeout' => (float) $this->timeoutSeconds],
                ]),
            ]);
        } catch (SoapFault $exception) {
            throw PacConnectionException::whileCreatingClient($wsdlUrl, $exception);
        }

        try {
            $response = $client->__soapCall($operation, [$params]);
        } catch (SoapFault $exception) {
            $this->log($client, 'Finkok:'.$operation.':ERROR', $logName);

            throw PacConnectionException::whileCalling($operation, $exception);
        }

        $this->log($client, 'Finkok:'.$operation, $logName);

        if (! $response instanceof stdClass) {
            throw PacUnexpectedResponseException::notAnObject($operation);
        }

        return $response;
    }

    private function log(SoapClient $client, string $title, string $logName): void
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/'.$logName.'.log'),
        ])->info($title, [
            'request' => $this->redactPassword((string) $client->__getLastRequest()),
            'response' => (string) $client->__getLastResponse(),
        ]);
    }

    /*
     * La contraseña del PAC viaja en texto plano dentro del cuerpo SOAP;
     * nunca debe quedar escrita en los logs.
     */
    private function redactPassword(string $xml): string
    {
        return (string) preg_replace(
            '/(<(?:\w+:)?password>).*?(<\/(?:\w+:)?password>)/is',
            '$1[REDACTADO]$2',
            $xml
        );
    }
}
