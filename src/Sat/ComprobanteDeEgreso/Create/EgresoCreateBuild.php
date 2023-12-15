<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Create;

use CfdiUtils\CfdiCreator40;
use JiagBrody\LaravelFacturaMx\Sat\Helper\DraftBuild;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PdfFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Helper\XmlFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use PhpCfdi\Credentials\Credential;

class EgresoCreateBuild extends DraftBuild
{
    public function __construct(protected Credential $credential, protected CfdiCreator40 $creatorCfdi, protected InvoiceCompanyHelper $companyHelper)
    {
    }

    public function saveDraft(): void
    {
        $this->saveDraftDetails();

        $this->invoice->refresh();

        $data = $this->getObjectFromComprobanteData();

        //TODO: VER LA MANERA DE GUARDAR EL TOTAL POR CFDI.
        /*if ($data->CfdiRelacionados) {
            foreach ($data->CfdiRelacionados as $relacionados) {
                if ($relacionados->TipoRelacion === CfdiRelationTypeEnum::NOTA_DE_CREDITO->value) {
                    foreach ($relacionados->CfdiRelacionado as $relacionado) {
                        $cfdi = $this->invoice->relatedCfdis->where('uuid', $relacionado->UUID)->firstOrFail();
                        $this->invoice->invoiceRefunds()->create([
                            'cfdi_id'  => $cfdi->id,
                            'subtotal' => $relacionado->TOTAL % 1.16,
                            'iva'      => $relacionado->TOTAL * 0.16,
                            'total'    => $relacionado->TOTAL,
                        ]);
                    }
                }
            }
        }*/

        $this->invoice->relatedCfdis->each(function ($item) {

            $this->invoice->invoiceRefunds()->create([
                'cfdi_id' => $item->id,
                'subtotal' => $item->id,
                'iva' => $item->id,
                'total' => $item->id,
            ]);
        });

        $xml = (new XmlFileSatHelperBuilder($this->invoice))->generate($this->creatorCfdi->asXml());
        (new PdfFileSatHelperBuilder())
            ->setInvoiceCfdiType($this->invoice->invoice_cfdi_type_id)
            ->setXmlContent($this->creatorCfdi->asXml())
            ->setXmlDocument($xml)
            ->build();
    }
}
