<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\CfdiCreator40;
use CfdiUtils\XmlResolver\XmlResolver;
use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\EmisorAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoRetenidoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ReceptorAtributos;
use PhpCfdi\Credentials\Credential;

abstract class CfdiHelperAbstract
{
    protected Credential $credential;

    protected \CfdiUtils\CfdiCreator40 $creatorCfdi;

    protected InvoiceCompanyHelper $companyHelper;

    protected AttributeAssembly $attributeAssembly;

    public function __construct()
    {
        $this->credential = Credential::openFiles($this->companyHelper->certificatePath, $this->companyHelper->keyPath, $this->companyHelper->passPhrase);
        $this->creatorCfdi = new CfdiCreator40();
        $this->creatorCfdi->putCertificado(new Certificado($this->credential->certificate()->pem()), false);
        $this->creatorCfdi->setXmlResolver(new XmlResolver(config('factura-mx.sat_local_resource_path')));
        $this->attributeAssembly = new AttributeAssembly;

        $this->addEmisor();
    }

    public function addRelacionados(array $relacionados): self
    {
        foreach ($relacionados as $k => $relacionado) {
            $this->creatorCfdi->comprobante()->addCfdiRelacionados([
                'TipoRelacion' => $relacionado['TipoRelacion'],
            ])->multiCfdiRelacionado(...$relacionado['CfdiRelacionado']);
        }

        return $this;
    }

    private function addEmisor(): void
    {
        $emisorAtributos = new EmisorAtributos;
        $emisorAtributos->setNombre($this->companyHelper->nombre);
        $emisorAtributos->setRfc($this->companyHelper->rfc);
        $emisorAtributos->setRegimenFiscal($this->companyHelper->regimenFiscal);

        $this->attributeAssembly->setEmisorAtributos($emisorAtributos);

        $this->creatorCfdi->comprobante()->addEmisor($emisorAtributos->getCollection()->toArray());
    }

    public function addReceptor(ReceptorAtributos $receptor): self
    {
        $this->attributeAssembly->setReceptorAtributos($receptor);

        $this->creatorCfdi->comprobante()->addReceptor($receptor->getCollection()->toArray());

        return $this;
    }

    public function addConceptos(Collection $concepts): self
    {
        if ($concepts->count() > 0) {
            $concepts->each(function (Collection $concept) {

                $item = $concept->get('conceptSat');
                $invoiceConcept = $this->creatorCfdi->comprobante()
                    ->addConcepto($item->getOnlySimplePropertiesCollection()->toArray());

                $sumT = collect();
                $item->getImpuestoTraslados()->each(function (ImpuestoTrasladoAtributos $transfer) use (
                    $invoiceConcept,
                    $sumT
                ) {
                    $array = $transfer->getCollection()->toArray();
                    $sumT->push($array);
                    $invoiceConcept->addTraslado($array);
                });
                $concept->put('total_transfer_taxes', $sumT->sum('Importe'));

                $sumR = collect();
                $item->getImpuestoRetenidos()->each(function (ImpuestoRetenidoAtributos $retention) use (
                    $invoiceConcept,
                    $sumR
                ) {
                    $array = $retention->getCollection()->toArray();
                    $sumR->push($array);
                    $invoiceConcept->addRetencion($array);
                });
                $concept->put('total_retention_taxes', $sumT->sum('Importe'));
            });
            $this->attributeAssembly->setConceptos($concepts);
        }

        return $this;
    }
}
