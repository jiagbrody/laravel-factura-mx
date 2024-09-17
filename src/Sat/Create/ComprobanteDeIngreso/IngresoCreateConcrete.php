<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use CfdiUtils\Elements\ImpLocal10\ImpuestosLocales;
use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTaxTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\RetencionesLocalesAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\TrasladosLocalesAtributos;

class IngresoCreateConcrete extends CfdiHelperAbstract
{
    public function __construct(InvoiceCompany $invoiceCompany)
    {
        $this->companyHelper = new InvoiceCompanyHelper($invoiceCompany);
        parent::__construct();
    }

    public function addAtributos(ComprobanteAtributos $attributes): self
    {
        $this->attributeAssembly->setComprobanteAtributos($attributes);

        $this->creatorCfdi->comprobante()->addAttributes($attributes->getCollection()->toArray());

        return $this;
    }

    public function addComplementoImpuestosLocales(Collection $localTaxes): self
    {
        $impLocales = new ImpuestosLocales;
        $format = collect();
        foreach ($localTaxes as $localTax) {
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

    public function builder(): IngresoCreateBuilder
    {
        $this->creatorCfdi->addSumasConceptos(null, 2);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();
        $this->creatorCfdi->addSello($this->credential->privateKey()->pem(), $this->credential->privateKey()->passPhrase());

        // Guardo los valores mutados por la librería (phpcfdi) para obtener "total, subtotal, descuento" de acuerdo a lo que exista en el CFDI.
        // Por ejemplo: Cuando se agrega el complemento de "Impuesto Local" se realiza el descuento sobre el total.
        $this->attributeAssembly->getComprobanteAtributos()->setInternallyAddTotalSubtotalDiscountValues($this->creatorCfdi->comprobante());

        return new IngresoCreateBuilder(
            credential: $this->credential,
            creatorCfdi: $this->creatorCfdi,
            companyHelper: $this->companyHelper,
            attributeAssembly: $this->attributeAssembly
        );
    }
}
