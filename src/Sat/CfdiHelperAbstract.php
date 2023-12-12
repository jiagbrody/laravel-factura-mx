<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use CfdiUtils\CfdiCreator40;
use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\EmisorAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ReceptorAtributos;
use PhpCfdi\Credentials\Credential;

abstract class CfdiHelperAbstract
{
    const IMPUESTO_TRASLADO_ATRIBUTOS_KEY = 'impuestoTrasladoAtributos';

    protected Credential $credential;

    protected \CfdiUtils\CfdiCreator40 $creatorCfdi;

    protected InvoiceCompanyHelper $companyHelper;

    protected AttributeAssembly $attributeAssembly;

    public function __construct()
    {
        $this->creatorCfdi = new CfdiCreator40();
        $this->credential = Credential::openFiles($this->companyHelper->certificatePath, $this->companyHelper->keyPath, $this->companyHelper->passPhrase);
        $this->attributeAssembly = new AttributeAssembly;

        $this->addEmisor();
    }

    public function addRelacionados(array $relacionados): self
    {
        foreach ($relacionados as $relacionado) {
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

    public function addConceptos(Collection $products): self
    {
        if ($products->count() > 0) {
            $products->each(function (ConceptoAtributos $item) {
                $this->creatorCfdi->comprobante()
                    ->addConcepto((array) $this->getConcept($item))
                    ->addTraslado((array) $this->getTraslado($item->getImpuestoTrasladoAtributos()));
            });
            $this->attributeAssembly->setConceptos($products);
        }

        return $this;
    }

    private function getConcept(ConceptoAtributos $product): array
    {
        $array = $product->getCollection();
        $array->forget(self::IMPUESTO_TRASLADO_ATRIBUTOS_KEY);

        return $array->toArray();
    }

    private function getTraslado(ImpuestoTrasladoAtributos $traslado): array
    {
        return $traslado->getCollection()->toArray();
    }
}
