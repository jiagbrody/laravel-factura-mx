<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\CfdiCreator40;
use CfdiUtils\XmlResolver\XmlResolver;
use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\CfdiRelacionadosAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\EmisorAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoRetenidoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ReceptorAtributos;
use PhpCfdi\Credentials\Credential;

abstract class CfdiHelperAbstract
{
    protected Credential $credential;

    protected CfdiCreator40 $creatorCfdi;

    protected InvoiceCompanyHelper $companyHelper;

    protected AttributeAssembly $attributeAssembly;

    public function __construct()
    {
        $this->credential = Credential::openFiles($this->companyHelper->certificatePath, $this->companyHelper->keyPath, $this->companyHelper->passPhrase);
        $this->creatorCfdi = new CfdiCreator40;
        $this->creatorCfdi->putCertificado(new Certificado($this->credential->certificate()->pem()), false);
        $this->creatorCfdi->setXmlResolver(new XmlResolver(config('jiagbrody-laravel-factura-mx.sat_local_resource_path')));
        $this->attributeAssembly = new AttributeAssembly;

        $this->addEmisor();
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

    public function addAtributos(ComprobanteAtributos $attributes): self
    {
        $this->attributeAssembly->setComprobanteAtributos($attributes);
        $this->creatorCfdi->comprobante()->addAttributes($attributes->getCollection()->toArray());

        return $this;
    }

    public function addConceptos(Collection $concepts): self
    {
        if ($concepts->count() > 0) {
            $concepts->each(function (ConceptoAtributos $concept) {

                // $item = $concept->get('conceptSat');
                // $item = $concept;
                $invoiceConcept = $this->creatorCfdi->comprobante()
                    ->addConcepto($concept->getOnlySimplePropertiesCollection()->toArray());

                $sumT = collect();
                $concept->getImpuestoTraslados()->each(function (ImpuestoTrasladoAtributos $transfer) use (
                    $invoiceConcept,
                    $sumT
                ) {
                    $array = $transfer->getCollection()->toArray();
                    $sumT->push($array);
                    $invoiceConcept->addTraslado($array);
                });
                // $concept->put('total_transfer_taxes', $sumT->sum('Importe'));

                $sumR = collect();
                $concept->getImpuestoRetenidos()->each(function (ImpuestoRetenidoAtributos $retention) use (
                    $invoiceConcept,
                    $sumR
                ) {
                    $array = $retention->getCollection()->toArray();
                    $sumR->push($array);
                    $invoiceConcept->addRetencion($array);
                });
                // $concept->put('total_retention_taxes', $sumR->sum('Importe'));
            });
            $this->attributeAssembly->setConceptos($concepts);
        }

        return $this;
    }

    public function addRelacionados(Collection $relationships): self
    {
        $relationships->each(function (CfdiRelacionadosAtributos $relacionado) {
            $this->creatorCfdi->comprobante()->addCfdiRelacionados([
                'TipoRelacion' => $relacionado->getTipoRelacion(),
            ])->multiCfdiRelacionado(...($relacionado->getCfdiRelacionado()->toArray()));
        });

        $this->attributeAssembly->setCfdiRelacionados($relationships);

        return $this;
    }

    public function getAttributeAssembly(): ComprobanteAtributos
    {
        return $this->attributeAssembly;
    }
}
