<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

final readonly class ImpuestoTrasladoAtributos
{
    use AtributosHelperTrait;

    private string $Base;

    private string $Impuesto;

    private string $TipoFactor;

    private string $TasaOCuota;

    private string $Importe;

    public function setBase(float $Base): void
    {
        $this->Base = (string) PatronDeDatosHelper::t_import($Base);
    }

    public function getBase(): string
    {
        return $this->Base;
    }

    public function setImpuesto(string $Impuesto): void
    {
        $this->Impuesto = $Impuesto;
    }

    public function getImpuesto(): string
    {
        return $this->Impuesto;
    }

    public function setTipoFactor(string $TipoFactor): void
    {
        $this->TipoFactor = $TipoFactor;
    }

    public function getTipoFactor(): string
    {
        return $this->TipoFactor;
    }

    public function setTasaOCuota(string $TasaOCuota): void
    {
        $this->TasaOCuota = $TasaOCuota;
    }

    public function getTasaOCuota(): string
    {
        return $this->TasaOCuota;
    }

    public function setImporte(float $Importe): void
    {
        $this->Importe = (string) PatronDeDatosHelper::t_import($Importe);
    }

    public function getImporte(): string
    {
        return $this->Importe;
    }
}
