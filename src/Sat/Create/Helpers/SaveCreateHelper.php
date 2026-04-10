<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;

// use JiagBrody\LaravelFacturaMx\Models\InvoiceIncome;

class SaveCreateHelper
{
    public function __construct(protected AttributeAssembly $attributeAssembly)
    {
    }

    public function createInvoice(int $companyHelperId): Invoice
    {
        $attributes = $this->attributeAssembly->getComprobanteAtributos();

        $invoice = new Invoice;
        $invoice->user_id = auth()->id();
        $invoice->invoice_type_id = InvoiceTypeEnum::getFirstBySatCode(satCode: $attributes->getTipoDeComprobante())->value;
        $invoice->invoice_company_id = $companyHelperId;
        $invoice->invoice_status_id = InvoiceStatusEnum::DRAFT->value;
        $invoice->invoice_date = $attributes->getFecha();
        $invoice->serie = $attributes->getSerie();
        $invoice->folio = $attributes->getFolio();
        $invoice->rfc_emisor = '';
        $invoice->rfc_receptor = '';
        $invoice->version = $attributes->getVersion();
        $invoice->save();

        return $invoice;
    }
}
