<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Stamp;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PdfFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Helper\XmlFileSatHelperBuilder;

class CreateStampConcrete
{
    public function __construct(
        protected Invoice $invoice,
        protected string $uuid,
        protected string $xml,
    ) {
    }

    public function updateInvoice(): self
    {
        $this->invoice->invoice_status_id = InvoiceStatusEnum::VIGENT->value;
        $this->invoice->save();
        $this->invoice->load('invoiceDetail');

        return $this;
    }

    public function createCfdi(): self
    {
        $invoiceCfdi = new InvoiceCfdi;
        $invoiceCfdi->user_id = auth()->id();
        $invoiceCfdi->invoice_id = $this->invoice->id;
        $invoiceCfdi->uuid = $this->uuid;
        $invoiceCfdi->save();

        $this->invoice->refresh();

        return $this;
    }

    public function generateDocuments(): self
    {
        dd('ok', $this);
        $xml = (new XmlFileSatHelperBuilder($this->invoice))
            ->updateModel($this->invoice->cfdi)
            ->updatePath('/cfdis/'.$this->invoice->invoiceDetail->emisor_rfc.'/'.$this->invoice->invoiceDetail->receptor_rfc.'/'.$this->invoice->invoiceDetail->fecha->format('Y').'/'.$this->invoice->invoiceDetail->tipo_de_comprobante.'/'.$this->invoice->invoiceDetail->fecha->format('m').'/'.$this->invoice->invoiceDetail->fecha->format('d'))
            ->updateFileName('invoice-'.$this->invoice->id.'-'.$this->invoice->cfdi->uuid)
            ->generate($this->xml);

        (new PdfFileSatHelperBuilder())
            ->setInvoiceCfdiType($this->invoice->invoice_cfdi_type_id)
            ->setXmlContent($this->xml)
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
