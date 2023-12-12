<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\EmisorAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ReceptorAtributos;

readonly class AttributeAssembly
{
    protected ComprobanteAtributos $comprobanteAtributos;

    protected EmisorAtributos $emisorAtributos;

    protected ReceptorAtributos $receptorAtributos;

    protected Collection $conceptos;

    protected Collection $complementoImpuestosLocales;

    public function setComprobanteAtributos(ComprobanteAtributos $comprobanteAtributos): void
    {
        $this->comprobanteAtributos = $comprobanteAtributos;
    }

    public function getComprobanteAtributos(): ComprobanteAtributos
    {
        return $this->comprobanteAtributos;
    }

    public function setEmisorAtributos(EmisorAtributos $emisorAtributos): void
    {
        $this->emisorAtributos = $emisorAtributos;
    }

    public function getEmisorAtributos(): EmisorAtributos
    {
        return $this->emisorAtributos;
    }

    public function setReceptorAtributos(ReceptorAtributos $receptorAtributos): void
    {
        $this->receptorAtributos = $receptorAtributos;
    }

    public function getReceptorAtributos(): ReceptorAtributos
    {
        return $this->receptorAtributos;
    }

    public function setConceptos(Collection $conceptos): void
    {
        $this->conceptos = $conceptos;
    }

    public function getConceptos(): Collection
    {
        return $this->conceptos;
    }

    public function setComplementoImpuestosLocales(Collection $complementoImpuestosLocales): void
    {
        $this->complementoImpuestosLocales = $complementoImpuestosLocales;
    }

    public function getComplementoImpuestosLocales(): Collection
    {
        return $this->complementoImpuestosLocales;
    }
}
