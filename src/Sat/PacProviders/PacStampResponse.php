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

    public function setIncidenciaIdIncidencia(string $incidenciaIdIncidencia): void
    {
        $this->incidenciaIdIncidencia = $incidenciaIdIncidencia;
    }

    public function getIncidenciaIdIncidencia(): string
    {
        return $this->incidenciaIdIncidencia;
    }

    public function setFullResponse($pacResponse)
    {




        return $result;
    }
}
