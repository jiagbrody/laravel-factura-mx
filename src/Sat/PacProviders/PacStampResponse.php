<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

final readonly class PacStampResponse
{
    public bool $checkProcess;

    public string $xml;

    public string $uuid;

    public string $codEstatus;

    public string $incidenciaIdIncidencia;

    public string $incidenciaCodigoError;

    public string $incidenciaMensaje;

    public function setCheckProcess(bool $checkProcess): void
    {
        $this->checkProcess = $checkProcess;
    }

    public function getCheckProcess(): bool
    {
        return $this->checkProcess;
    }

    public function setXml(string $xml): void
    {
        $this->xml = $xml;
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setCodEstatus(string $codEstatus): void
    {
        $this->codEstatus = $codEstatus;
    }

    public function getCodEstatus(): string
    {
        return $this->codEstatus;
    }

    public function setIncidenciaCodigoError(string $incidenciaCodigoError): void
    {
        $this->incidenciaCodigoError = $incidenciaCodigoError;
    }

    public function getIncidenciaCodigoError(): string
    {
        return $this->incidenciaCodigoError;
    }

    public function setIncidenciaMensaje(string $incidenciaMensaje): void
    {
        $this->incidenciaMensaje = $incidenciaMensaje;
    }

    public function getIncidenciaMensaje(): string
    {
        return $this->incidenciaMensaje;
    }
}
