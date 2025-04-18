<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use CfdiUtils\Elements\ImpLocal10\ImpuestosLocales;
use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTaxTypeEnum;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\RetencionesLocalesAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\TrasladosLocalesAtributos;

trait AddComplementoImpuestosLocalesTrait
{
    public function addComplementoImpuestosLocales(Collection $impuestosLocales): self
    {
        $impLocales = new ImpuestosLocales;
        $format = collect();
        foreach ($impuestosLocales as $localTax) {
            if ($localTax instanceof RetencionesLocalesAtributos) {
                $format->push(array_merge($localTax->getCollection()->toArray(), ['invoice_tax_type_id' => InvoiceTaxTypeEnum::RETENCION->value]));
                $impLocales->addRetencionLocal($localTax->getCollection()->toArray());
            } elseif ($localTax instanceof TrasladosLocalesAtributos) {
                $format->push(array_merge($localTax->getCollection()->toArray(), ['invoice_tax_type_id' => InvoiceTaxTypeEnum::TRASLADO->value]));
                $impLocales->addTrasladoLocal(($localTax->getCollection()->toArray()));
            }
        }

        $this->attributeAssembly->setComplementoImpuestosLocales($format);

        $this->creatorCfdi->comprobante()->addComplemento($impLocales);

        return $this;
    }
}
