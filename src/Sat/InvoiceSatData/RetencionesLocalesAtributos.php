<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

final readonly class RetencionesLocalesAtributos
{
    use AtributosHelperTrait;

    private string $ImpLocRetenido;

    private string $TasadeRetencion; // asi está declarado el nombre para el SAT

    private string $Importe;

    public function setImpLocRetenido(string $ImpLocRetenido): void
    {
        $this->ImpLocRetenido = $ImpLocRetenido;
    }

    public function getImpLocRetenido(): string
    {
        return $this->ImpLocRetenido;
    }

    public function setTasaDeRetencion(string $TasaDeRetencion): void
    {
        $this->TasadeRetencion = $TasaDeRetencion;
    }

    public function getTasaDeRetencion(): string
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
