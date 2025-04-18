<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso\EgresoCreateBuild;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;

class GenericCreator extends CfdiHelperAbstract
{
    use AddComplementoImpuestosLocalesTrait;

    public function __construct(InvoiceCompany $invoiceCompany)
    {
        $this->companyHelper = new InvoiceCompanyHelper($invoiceCompany);
        parent::__construct();
    }

    public function build(): CreateBuild
    {
        $this->creatorCfdi->addSumasConceptos(null, 2);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();
        $this->creatorCfdi->addSello($this->credential->privateKey()->pem(), $this->credential->privateKey()->passPhrase());

        return new CreateBuild(
            xmlContent: $this->creatorCfdi->asXml(),
            companyHelper: $this->companyHelper,
            attributeAssembly: $this->attributeAssembly
        );
    }
}
