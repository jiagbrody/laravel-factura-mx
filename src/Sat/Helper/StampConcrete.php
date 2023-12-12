<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use App\Enums\CfdiStatusEnum;
use App\Models\Cfdi;
use App\Models\Invoice;
use App\Services\Documentable\DocumentDestroyService;
use App\Services\PAC\Providers\PacStampResponse;

abstract class StampConcrete implements StampConcreteInterface
{
    public function __construct(protected Invoice $invoice, protected PacStampResponse $pacStampResponse)
    {
    }

    public function removeInvoiceDraft(): self
    {
        $this->invoice->is_draft = false;
        $this->invoice->save();

        return $this;
    }

    public function createCfdi(): self
    {
        Cfdi::create([
            'invoice_id' => $this->invoice->id,
            'cfdi_status_id' => CfdiStatusEnum::VALID->value,
            'uuid' => $this->pacStampResponse->uuid,
        ]);
        $this->invoice->refresh();

        return $this;
    }

    public function generateDocumentsFromCfdi(): self
    {
        $xml = (new XmlFileSatHelperBuilder($this->invoice))
            ->updateModel($this->invoice->cfdi)
            ->updatePath('/cfdis/'.$this->invoice->invoiceDetail->emisor_rfc.'/'.$this->invoice->invoiceDetail->receptor_rfc.'/'.$this->invoice->invoiceDetail->fecha->format('Y').'/'.$this->invoice->invoiceDetail->tipo_de_comprobante.'/'.$this->invoice->invoiceDetail->fecha->format('m').'/'.$this->invoice->invoiceDetail->fecha->format('d'))
            ->updateFileName('invoice-'.$this->invoice->id.'-'.$this->invoice->cfdi->uuid)
            ->generate($this->pacStampResponse->xml);

        (new PdfFileSatHelperBuilder())
            ->setInvoiceCfdiType($this->invoice->invoice_cfdi_type_id)
            ->setXmlContent($this->pacStampResponse->xml)
            ->setXmlDocument($xml)
            ->build();

        // BORRO LOS DOCUMENTOS DE BORRADOR.
        if ($this->invoice->documents()->exists()) {
            $this->invoice->documents()->each(function ($document) {
                (new DocumentDestroyService($document))->make();
            });
        }

        return $this;
    }
}
