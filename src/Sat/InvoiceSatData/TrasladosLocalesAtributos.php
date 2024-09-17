<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

final readonly class TrasladosLocalesAtributos
{
    use AtributosHelperTrait;

    private string $ImpLocTrasladado;

    private string $TasadeTraslado;

    private string $Importe;

    public function setImpLocTrasladado(string $ImpLocTrasladado): void
    {
        $this->ImpLocTrasladado = $ImpLocTrasladado;
    }

    public function getImpLocTrasladado(): string
    {
        return $this->ImpLocTrasladado;
    }

    public function setTasadeTraslado(string $TasadeTraslado): void
    {
        $this->TasadeTraslado = $TasadeTraslado;
    }

    public function getTasadeTraslado(): string
    {
        return $this->TasadeTraslado;
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
