<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

/**
 * Resultado inmutable de la solicitud de cancelación ante el SAT.
 *
 * checkProcess=true no significa "cancelada": la cancelación puede quedar
 * pendiente de aceptación del receptor. Hay que consultar el estatus del CFDI
 * hasta que cambie a "Cancelado".
 */
final class PacCancelResponse
{
    private function __construct(
        public readonly bool $checkProcess,
        public readonly string $uuid,
        public readonly string $estatusUUID,
        public readonly string $estatusCancelacion,
        public readonly string $acuse,
    ) {}

    /**
     * EstatusUUID 201: petición de cancelación aceptada, con acuse.
     */
    public static function accepted(string $uuid, string $acuse): self
    {
        return new self(
            checkProcess: true,
            uuid: $uuid,
            estatusUUID: '201',
            estatusCancelacion: 'Petición de cancelación realizada exitosamente',
            acuse: $acuse,
        );
    }

    /**
     * EstatusUUID 202: el UUID ya estaba cancelado; el SAT no devuelve acuse
     * nuevo (el original se conservó cuando se aceptó la primera petición).
     */
    public static function previouslyCancelled(string $uuid): self
    {
        return new self(
            checkProcess: true,
            uuid: $uuid,
            estatusUUID: '202',
            estatusCancelacion: 'UUID previamente cancelado',
            acuse: '',
        );
    }

    public static function rejected(string $uuid, string $estatusUUID, string $estatusCancelacion): self
    {
        return new self(
            checkProcess: false,
            uuid: $uuid,
            estatusUUID: $estatusUUID,
            estatusCancelacion: $estatusCancelacion,
            acuse: '',
        );
    }

    public function getCheckProcess(): bool
    {
        return $this->checkProcess;
    }

    public function hasAcuse(): bool
    {
        return $this->acuse !== '';
    }
}
