<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso\Stamp;

use App\Enums\CfdiStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Enums\StatementStatusEnum;
use App\Models\Cfdi;
use App\Models\StatementCompanyAgreement;
use App\Services\Documentable\DocumentDestroyService;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PdfFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Helper\XmlFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;

class IngresoStampConcrete
{
    public function __construct(protected Invoice $invoice, protected PacStampResponse $pacStampResponse)
    {
    }

    public function updateInvoice(): IngresoStampConcrete
    {
        $this->invoice->is_draft = false;
        $this->invoice->save();
        $this->invoice->load('invoiceDetail');

        return $this;
    }

    public function createCfdi(): IngresoStampConcrete
    {
        Cfdi::create([
            'invoice_id' => $this->invoice->id,
            'cfdi_status_id' => CfdiStatusEnum::VALID->value,
            'uuid' => $this->pacStampResponse->uuid,
        ]);
        $this->invoice->refresh();

        return $this;
    }

    public function generateDocuments(): IngresoStampConcrete
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

    public function changeStatusRelationshipWithInvoice(): self
    {
        $statementStatusId = StatementStatusEnum::ACCOUNT_BILLED_AND_PAID->value;
        if ($this->invoice->invoiceDetail->metodo_pago === 'PPD') {
            $statementStatusId = StatementStatusEnum::ACCOUNT_BILLED_RECEIVABLE->value;
        }

        if ($this->invoice->invoice_type_id === InvoiceTypeEnum::QUICK_SALE->value) {
            $this->invoice->quickSale->sale_status_id = SaleStatusEnum::BILLED->value;
            $this->invoice->quickSale->statement_status_id = $statementStatusId;
            $this->invoice->quickSale->save();
        } elseif ($this->invoice->invoice_type_id === InvoiceTypeEnum::PATIENT->value) {
            $this->invoice->statement->statementPatient->sale_status_id = SaleStatusEnum::BILLED->value;
            $this->invoice->statement->statementPatient->statement_status_id = $statementStatusId;
            $this->invoice->statement->statementPatient->save();
        } elseif ($this->invoice->invoice_type_id === InvoiceTypeEnum::AGREEMENT->value) {
            $agreement = StatementCompanyAgreement::query()
                ->whereId($this->invoice->statement_company_agreement_id)
                ->whereStatementId($this->invoice->statement_id)
                ->first();
            $agreement->sale_status_id = SaleStatusEnum::BILLED->value;
            $agreement->statement_status_id = $statementStatusId;
            $agreement->save();
        } elseif ($this->invoice->invoice_type_id === InvoiceTypeEnum::GLOBAL->value) {
            $this->invoice->globalInvoice->sale_status_id = SaleStatusEnum::BILLED->value;
            $this->invoice->globalInvoice->statement_status_id = StatementStatusEnum::ACCOUNT_BILLED_AND_PAID->value;
            $this->invoice->globalInvoice->save();
        }

        return $this;
    }
}
