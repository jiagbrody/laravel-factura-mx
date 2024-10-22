<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\EmisorAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoRetenidoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ReceptorAtributos;
use ReflectionProperty;

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

    public function getConceptos($completelyTransformIntoACollect = false): Collection
    {
        if ($completelyTransformIntoACollect) {
            $collect = collect();
            $this->conceptos->each(function (ConceptoAtributos $item) use ($collect) {
                $transfers = collect();
                $item->getImpuestoTraslados()->each(function (ImpuestoTrasladoAtributos $traslado) use ($transfers) {
                    $transfers->push($traslado->getCollection());
                });

                $retentions = collect();
                $item->getImpuestoRetenidos()->each(function (ImpuestoRetenidoAtributos $retenido) use ($retentions) {
                    $retentions->push($retenido->getCollection());
                });

                $full = $item->getOnlySimplePropertiesCollection()
                    ->put(ConceptoAtributos::RELATED_NAME_CONCEPTO_TRASLADO, $transfers)
                    ->put(ConceptoAtributos::RELATED_NAME_CONCEPTO_RETENIDO, $retentions);

                $collect->push($full);
            });

            return $collect;
        }

        return $this->conceptos;
    }

    public function setComplementoImpuestosLocales(Collection $complementoImpuestosLocales): void
    {
        $this->complementoImpuestosLocales = $complementoImpuestosLocales;
    }

    public function getComplementoImpuestosLocales(): Collection
    {
        //ESTA PROPIEDAD ES OPCIONAL Y PUEDE O NO ESTAR INICIALIZADA
        $rp = new \ReflectionProperty(self::class, 'complementoImpuestosLocales');
        if ($rp->isInitialized($this) === false) {
            return collect();
        }

        return $this->complementoImpuestosLocales;
    }

    /*
     * Obtiene las propiedades de la clase, util para mostrarse en el frontend con los datos por default para facturar.
     */
    public function get(): \stdClass
    {
        $class = $this;
        $className = get_class($class);
        $properties = get_class_vars($className);

        $data = new \stdClass;

        foreach ($properties as $key => $value) {
            $rp = new ReflectionProperty($className, $key);
            if ($rp->isInitialized($class)) {
                $data->{$key} = clone $this->{$key};
            }
        }

        return $data;
    }
}
