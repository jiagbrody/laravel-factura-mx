<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos\Stamp;

use App\Enums\CfdiStatusEnum;
use JiagBrody\LaravelFacturaMx\Models\Cfdi;
use App\Services\Documentable\DocumentDestroyService;
use App\Services\PAC\Providers\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PdfFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Helper\XmlFileSatHelperBuilder;

class PagoStampConcrete
{
    public function __construct(protected Invoice $invoice, protected PacStampResponse $pacStampResponse)
    {
    }

    public function updateInvoice(): self
    {
        $this->invoice->is_draft = false;
        $this->invoice->save();
        $this->invoice->load('invoiceDetail');

        return $this;
    }

    public function updatePaymentDocumentsAsActive(): self
    {
        $this->invoice->invoicePayments->each(function ($payment) {
            $payment->invoicePaymentDocuments()->update(['is_active' => true]);
        });

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

    public function generateDocuments(): self
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
