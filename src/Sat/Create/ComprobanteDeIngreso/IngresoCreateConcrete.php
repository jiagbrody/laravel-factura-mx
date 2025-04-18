<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;

class IngresoCreateConcrete extends CfdiHelperAbstract
{
    public function __construct(InvoiceCompany $invoiceCompany)
    {
        $this->companyHelper = new InvoiceCompanyHelper($invoiceCompany);
        parent::__construct();
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
            // credential: $this->credential,
            xmlContent: $this->creatorCfdi->asXml(),
            companyHelper: $this->companyHelper,
            attributeAssembly: $this->attributeAssembly
        );
    }
}
