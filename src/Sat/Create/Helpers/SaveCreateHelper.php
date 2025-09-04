<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;

// use JiagBrody\LaravelFacturaMx\Models\InvoiceIncome;

class SaveCreateHelper
{
    public function __construct(protected AttributeAssembly $attributeAssembly) {}

    public function createInvoice(int $companyHelperId): Invoice
    {
        $attributes = $this->attributeAssembly->getComprobanteAtributos();
        // $emisor = $this->attributeAssembly->getEmisorAtributos();
        // $receptor = $this->attributeAssembly->getReceptorAtributos();

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

    // public function saveIncome($invoice): void
    // {
    //     // WILL ALWAYS BE SAVED
    //     $invoiceIncome = $invoice->invoiceIncome ?? new InvoiceIncome;
    //     $attributes = $this->attributeAssembly->getComprobanteAtributos();
    //     $invoiceIncome->invoice_id = $invoice->id;
    //     $invoiceIncome->forma_pago = $attributes->getFormaPago();
    //     // $invoiceIncome->condiciones_de_pago = $attributes->getCondicionesDePago();
    //     $invoiceIncome->sub_total = $attributes->getSubTotal();
    //     $invoiceIncome->descuento = $attributes->getDescuento();
    //     $invoiceIncome->moneda = $attributes->getMoneda();
    //     $invoiceIncome->tipo_cambio = $attributes->getTipoCambio();
    //     $invoiceIncome->total = $attributes->getTotal();
    //     // $invoiceIncome->tipo_de_comprobante = $attributes->getTipoDeComprobante();
    //     $invoiceIncome->exportacion = $attributes->getExportacion();
    //     $invoiceIncome->metodo_pago = $attributes->getMetodoPago();
    //     $invoiceIncome->lugar_expedicion = $attributes->getLugarExpedicion();
    //     // $invoiceIncome->receptor_rfc = $this->attributeAssembly->getReceptorAtributos()->getRfc();
    //     $invoiceIncome->save();
    // }

    /*
     * ESTE PROCESO DE GUARDAR RELACIONADOS PODRIA IR EN UNA CLASE APARTE Y TAL VEZ LLAMARLA CON UN "trait" YA QUE SE UTILIZARA EN LAS DEMAS FACTURAS:
     * INGRESO, EGRESO, COMPLEMENTO PAGO.
     */
    // public function saveRelationshipsAddOn($invoice): void
    // {
    //     if ($this->attributeAssembly->getCfdiRelacionados()->count()) {
    //         $this->attributeAssembly->getCfdiRelacionados()->each(function (CfdiRelacionadosAtributos $item) use ($invoice) {
    //             $item->getCfdiRelacionado()->each(function ($item2) use ($invoice) {
    //                 $cfdi = InvoiceCfdi::where('uuid', $item2->first())->first();
    //                 $cfdiRelationship = new InvoiceRelationship;
    //                 $cfdiRelationship->origin_invoice_id = $invoice->id;
    //                 $cfdiRelationship->related_invoice_id = $cfdi->invoice_id;
    //                 $cfdiRelationship->invoice_relationship_type_id = 1;
    //                 $cfdiRelationship->relationship_date = now();
    //                 $cfdiRelationship->save();
    //             });
    //         });
    //     }
    // }
    //
    // public function saveInvoiceBalances(): void
    // {
    //     $invoiceBalance = $invoice->invoiceBalance ?? new InvoiceBalance;
    //     $concepts = $this->attributeAssembly->getConceptos();
    //
    //     $invoiceBalance->invoice_id = $invoice->id;
    //     $invoiceBalance->gross_sub_total = $concepts->sum('gross_sub_total');
    //     $invoiceBalance->sub_total = $concepts->sum('sub_total');
    //     $invoiceBalance->discount = $concepts->sum('discount');
    //     $invoiceBalance->tax = $concepts->sum('tax');
    //     $invoiceBalance->total = $concepts->sum('total');
    //     $invoiceBalance->final_total_balance = null; // TODO: ES LA RESTA DEL IMPUESTO LOCAL PERO NO SE CONTRA QUE SI TOTAL O SUBTOTAL.
    //     $invoiceBalance->is_paid = ComprobanteDeIngresoRuleHelper::getIsPaid($this->attributeAssembly->getComprobanteAtributos()->getMetodoPago());
    //     $invoiceBalance->invoice_payment_type_id = ComprobanteDeIngresoRuleHelper::getPaymentTypeId($this->attributeAssembly->getComprobanteAtributos()->getMetodoPago());
    //     $invoiceBalance->save();
    // }
    //
    // public function saveInvoiceTaxes(): void
    // {
    //     // WILL ALWAYS BE SAVED
    //     $invoiceTax = $invoice->invoiceTax ?? new InvoiceTax;
    //     $concepts = $this->attributeAssembly->getConceptos();
    //
    //     $invoiceTax->invoice_id = $invoice->id;
    //     $invoiceTax->total_impuestos_retenidos = $concepts->sum('total_retention_taxes');
    //     $invoiceTax->total_impuestos_trasladados = $concepts->sum('total_transfer_taxes');
    //     $invoiceTax->save();
    //
    //     $this->toInvoiceTaxDetails($invoiceTax, $concepts);
    // }
    //
    // public function saveComplementLocalTax(): void
    // {
    //     if ($localTaxes->isNotEmpty()) {
    //         $complementLocalTax = new InvoiceComplementLocalTax;
    //         $complementLocalTax->invoice_id = $invoice->id;
    //         $complementLocalTax->total_de_retenciones = $localTaxes->where('invoice_tax_type_id', InvoiceTaxTypeEnum::RETENCION->value)->sum('Importe');
    //         $complementLocalTax->total_de_traslados = $localTaxes->where('invoice_tax_type_id', InvoiceTaxTypeEnum::TRASLADO->value)->sum('Importe');
    //         $complementLocalTax->save();
    //
    //         foreach ($localTaxes as $localTax) {
    //             $localTaxDetail = new InvoiceComplementLocalTaxDetail;
    //             $localTaxDetail->invoice_complement_local_tax_id = $complementLocalTax->id;
    //             $localTaxDetail->invoice_tax_type_id = $localTax['invoice_tax_type_id'];
    //
    //             switch ($localTax['invoice_tax_type_id']) {
    //                 case InvoiceTaxTypeEnum::RETENCION->value:
    //                     $localTaxDetail->imp_loc_retenido = $localTax['ImpLocRetenido'];
    //                     $localTaxDetail->tasa_de_retencion = $localTax['TasadeRetencion'];
    //                     break;
    //                 case InvoiceTaxTypeEnum::TRASLADO->value:
    //                     $localTaxDetail->imp_loc_trasladado = $localTax['ImpLocTrasladado'];
    //                     $localTaxDetail->tasa_de_traslado = $localTax['TasadeTraslado'];
    //                     break;
    //             }
    //
    //             $localTaxDetail->importe = $localTax['Importe'];
    //             $localTaxDetail->save();
    //         }
    //     }
    // }
    //
    // private function toInvoiceTaxDetails(InvoiceTax $invoiceTax, Collection $concepts): void
    // {
    //     $concepts->each(function (Collection $concept) use ($invoiceTax) {
    //         $this->putRegisterTax($invoiceTax, $this->getAttributesConceptFromClient($concept));
    //     });
    // }
    //
    // private function putRegisterTax(InvoiceTax $invoiceTax, ConceptoAtributos $conceptoAtributos): void
    // {
    //     $conceptoAtributos->getImpuestoTraslados()->each(function (ImpuestoTrasladoAtributos $concept) use ($invoiceTax) {
    //         $this->saveTax($invoiceTax, $concept->getCollection(), InvoiceTaxTypeEnum::TRASLADO);
    //
    //     });
    //
    //     $conceptoAtributos->getImpuestoRetenidos()->each(function (ImpuestoRetenidoAtributos $concept) use ($invoiceTax) {
    //         $this->saveTax($invoiceTax, $concept->getCollection(), InvoiceTaxTypeEnum::RETENCION);
    //     });
    // }
    //
    // private function saveTax(InvoiceTax $invoiceTax, Collection $collect, InvoiceTaxTypeEnum $enum): void
    // {
    //     $data = new InvoiceTaxDetail;
    //
    //     $data->invoice_tax_id = $invoiceTax->id;
    //     $data->invoice_tax_type_id = $enum->value;
    //     $data->base = $collect->get('Base');
    //     $data->impuesto = $collect->get('Impuesto');
    //     $data->tipo_factor = $collect->get('TipoFactor');
    //     $data->tasa_o_cuota = $collect->get('TasaOCuota');
    //     $data->importe = $collect->get('Importe');
    //     $data->save();
    // }
    //
    // private function getAttributesConceptFromClient(): ConceptoAtributos
    // {
    //     return $collection->get('conceptSat');
    // }
}
