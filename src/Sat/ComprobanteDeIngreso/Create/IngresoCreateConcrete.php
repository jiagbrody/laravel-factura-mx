<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Create;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\Elements\ImpLocal10\ImpuestosLocales;
use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;

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

    public function addComplementoImpuestosLocales(Collection $impuestosLocales): self
    {
        if ($impuestosLocales->isNotEmpty()) {
            $impLocales = new ImpuestosLocales();
            $impuestosLocales->each(function (Collection $item) use ($impLocales) {
                $impLocales->addRetencionLocal($item->get('localTaxSat')->getCollection()->toArray());
            });
            $this->attributeAssembly->setComplementoImpuestosLocales($impuestosLocales);

            $this->creatorCfdi->comprobante()->addComplemento($impLocales);
        }

        return $this;
    }

    public function build(): IngresoCreateBuild
    {
        $this->creatorCfdi->putCertificado(new Certificado($this->credential->certificate()->pem()), false);
        $this->creatorCfdi->addSumasConceptos(null, 2);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();

        return (new IngresoCreateBuild(
            credential: $this->credential,
            creatorCfdi: $this->creatorCfdi,
            companyHelper: $this->companyHelper,
            attributeAssembly: $this->attributeAssembly
        ));
    }
}
