<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

/**
 * Resultado inmutable del timbrado. Se construye con los constructores
 * nombrados stamped()/rejected(), por lo que todos los getters son seguros
 * en cualquier estado (los campos que no aplican regresan cadena vacía).
 */
final class PacStampResponse
{
    private function __construct(
        private readonly bool $checkProcess,
        private readonly string $uuid,
        private readonly string $codEstatus,
        private readonly string $xml,
        private readonly string $incidenciaIdIncidencia,
        private readonly string $incidenciaCodigoError,
        private readonly string $incidenciaMensaje,
    ) {}

    public static function stamped(string $uuid, string $codEstatus, string $xml): self
    {
        return new self(
            checkProcess: true,
            uuid: $uuid,
            codEstatus: $codEstatus,
            xml: $xml,
            incidenciaIdIncidencia: '',
            incidenciaCodigoError: '',
            incidenciaMensaje: '',
        );
    }

    public static function rejected(
        string $codEstatus,
        string $incidenciaIdIncidencia,
        string $incidenciaCodigoError,
        string $incidenciaMensaje,
        string $uuid = '',
    ): self {
        return new self(
            checkProcess: false,
            uuid: $uuid,
            codEstatus: $codEstatus,
            xml: '',
            incidenciaIdIncidencia: $incidenciaIdIncidencia,
            incidenciaCodigoError: $incidenciaCodigoError,
            incidenciaMensaje: $incidenciaMensaje,
        );
    }

    public function getCheckProcess(): bool
    {
        return $this->checkProcess;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getCodEstatus(): string
    {
        return $this->codEstatus;
    }

    public function getXml(): string
    {
        return $this->xml;
    }

    public function getIncidenciaIdIncidencia(): string
    {
        return $this->incidenciaIdIncidencia;
    }

    public function getIncidenciaCodigoError(): string
    {
        return $this->incidenciaCodigoError;
    }

    public function getIncidenciaMensaje(): string
    {
        return $this->incidenciaMensaje;
    }
}
