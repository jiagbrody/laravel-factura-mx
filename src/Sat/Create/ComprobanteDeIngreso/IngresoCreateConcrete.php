<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use CfdiUtils\Elements\ImpLocal10\ImpuestosLocales;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\RetencionesLocalesAtributos;

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

    public function addComplementoImpuestosLocales(RetencionesLocalesAtributos $impuestosLocales): self
    {
        $impLocales = new ImpuestosLocales();
        $impLocales->addRetencionLocal($impuestosLocales->getCollection()->toArray());

        $this->attributeAssembly->setComplementoImpuestosLocales($impuestosLocales->getCollection());

        $this->creatorCfdi->comprobante()->addComplemento($impLocales);

        return $this;
    }

    public function build(): IngresoCreateBuild
    {
        $this->creatorCfdi->addSumasConceptos(null, 2);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();
        $this->creatorCfdi->addSello($this->credential->privateKey()->pem(), $this->credential->privateKey()->passPhrase());

        // Guardo los valores mutados por la librería (phpcfdi) para obtener "total, subtotal, descuento" de acuerdo a lo que exista en el CFDI.
        // Por ejemplo: Cuando se agrega el complemento de "Impuesto Local" se realiza el descuento sobre el total.
        $this->attributeAssembly->getComprobanteAtributos()->setInternallyAddTotalSubtotalDiscountValues($this->creatorCfdi->comprobante());

        return new IngresoCreateBuild(
            credential: $this->credential,
            creatorCfdi: $this->creatorCfdi,
            companyHelper: $this->companyHelper,
            attributeAssembly: $this->attributeAssembly
        );
    }
}
