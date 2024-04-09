<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

final readonly class PacStampResponse
{
    private bool $checkProcess;

    private string $xml;

    private string $uuid;

    private string $codEstatus;

    private string $incidenciaIdIncidencia;

    private string $incidenciaCodigoError;

    private string $incidenciaMensaje;

    private function setCheckProcess(bool $checkProcess): void
    {
        $this->checkProcess = $checkProcess;
    }

    public function getCheckProcess(): bool
    {
        return $this->checkProcess;
    }

    private function setXml(string $xml): void
    {
        $this->xml = $xml;
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    private function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    private function setCodEstatus(string $codEstatus): void
    {
        $this->codEstatus = $codEstatus;
    }

    public function getCodEstatus(): string
    {
        return $this->codEstatus;
    }

    private function setIncidenciaCodigoError(string $incidenciaCodigoError): void
    {
        $this->incidenciaCodigoError = $incidenciaCodigoError;
    }

    public function getIncidenciaCodigoError(): string
    {
        return $this->incidenciaCodigoError;
    }

    private function setIncidenciaMensaje(string $incidenciaMensaje): void
    {
        $this->incidenciaMensaje = $incidenciaMensaje;
    }

    public function getIncidenciaMensaje(): string
    {
        return $this->incidenciaMensaje;
    }

    private function setIncidenciaIdIncidencia(string $incidenciaIdIncidencia): void
    {
        $this->incidenciaIdIncidencia = $incidenciaIdIncidencia;
    }

    public function getIncidenciaIdIncidencia(): string
    {
        return $this->incidenciaIdIncidencia;
    }

    public function setFullResponse($pacResponse)
    {
        $result = $pacResponse->quick_stampResult;
        $this->setUuid($result->UUID ?? '');
        $this->setCodEstatus($result->CodEstatus ?? '');

        if (isset($result->CodEstatus) && ($result->CodEstatus === 'Comprobante timbrado satisfactoriamente')) {
            $this->setCheckProcess(true);
            $this->setXml($result->xml);

            return $this;
        }

        $incidencia = $result->Incidencias->Incidencia;
        $this->setIncidenciaIdIncidencia($incidencia->IdIncidencia);
        $message = $incidencia->MensajeIncidencia;
        if ($incidencia->MensajeIncidencia) {
            $message .= ' - ' . $incidencia->ExtraInfo;
        }
        $this->setIncidenciaMensaje($message);
        $this->setIncidenciaCodigoError($incidencia->CodigoError);

        return $result;
    }
}
