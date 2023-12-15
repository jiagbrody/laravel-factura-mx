<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

readonly final class RetencionesLocalesAtributos
{
    use AtributosHelperTrait;

    private string $ImpLocRetenido;
    private string $TasaDeRetencion;
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
        $this->TasaDeRetencion = $TasaDeRetencion;
    }

    public function getTasaDeRetencion(): string
    {
        return $this->TasaDeRetencion;
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
