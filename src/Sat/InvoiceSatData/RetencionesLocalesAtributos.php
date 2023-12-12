<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

final readonly class RetencionesLocalesAtributos
{
    use AtributosHelperTrait;

    private string $ImpLocRetenido;

    private string $TasadeRetencion;

    private string $Importe;

    public function setImpLocRetenido(string $ImpLocRetenido): void
    {
        $this->ImpLocRetenido = $ImpLocRetenido;
    }

    public function getImpLocRetenido(): string
    {
        return $this->ImpLocRetenido;
    }

    public function setTasadeRetencion(string $TasadeRetencion): void
    {
        $this->TasadeRetencion = $TasadeRetencion;
    }

    public function getTasadeRetencion(): string
    {
        return $this->TasadeRetencion;
    }

    public function setImporte(string $Importe): void
    {
        $this->Importe = $Importe;
    }

    public function getImporte(): string
    {
        return $this->Importe;
    }
}
