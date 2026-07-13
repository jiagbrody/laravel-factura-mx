<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;

/**
 * Resultado inmutable de la consulta de estatus del CFDI ante el SAT.
 *
 * invoiceStatusEnum es null cuando el estado del SAT no corresponde a ningún
 * estatus local ("No Encontrado", respuestas intermedias, etc.): en ese caso
 * NO debe modificarse el estatus de la factura; revisa el campo "estado".
 */
final class PacStatusResponse
{
    private function __construct(
        public readonly bool $checkProcess,
        public readonly string $estado,
        public readonly string $esCancelable,
        public readonly string $codigoEstatus,
        public readonly string $estatusCancelacion,
        public readonly string $validacionEFOS,
        public readonly string $detallesValidacionEFOS,
        public readonly ?InvoiceStatusEnum $invoiceStatusEnum,
    ) {}

    public static function fromSat(
        string $estado,
        string $esCancelable,
        string $codigoEstatus,
        string $estatusCancelacion,
        string $validacionEFOS,
        string $detallesValidacionEFOS,
    ): self {
        return new self(
            checkProcess: true,
            estado: $estado,
            esCancelable: $esCancelable,
            codigoEstatus: $codigoEstatus,
            estatusCancelacion: $estatusCancelacion,
            validacionEFOS: $validacionEFOS,
            detallesValidacionEFOS: $detallesValidacionEFOS,
            invoiceStatusEnum: match ($estado) {
                'Vigente' => InvoiceStatusEnum::VIGENT,
                'Cancelado' => InvoiceStatusEnum::CANCELED,
                default => null,
            },
        );
    }

    public function getCheckProcess(): bool
    {
        return $this->checkProcess;
    }

    public function getInvoiceStatusEnum(): ?InvoiceStatusEnum
    {
        return $this->invoiceStatusEnum;
    }
}
