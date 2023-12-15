<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTaxTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceBalance;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDetail;
use JiagBrody\LaravelFacturaMx\Models\InvoiceTax;
use JiagBrody\LaravelFacturaMx\Models\InvoiceTaxDetail;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoRetenidoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\Rules\ComprobanteDeIngresoRuleHelper;

class SaveIngreso implements SaveIngresoInterface
{
    public function __construct(protected AttributeAssembly $attributeAssembly)
    {
    }

    public function toInvoice($relationshipModel, $relationshipId, $companyHelperId): Invoice
    {
        $invoice = new Invoice;
        $invoice->user_id = auth()->id();
        $invoice->invoice_type_id = InvoiceTypeEnum::INGRESO->value;
        $invoice->invoice_company_id = $companyHelperId;
        $invoice->invoice_status_id = InvoiceStatusEnum::DRAFT->value;
        $invoice->invoiceable_type = $relationshipModel;
        $invoice->invoiceable_id = $relationshipId;
        $invoice->save();

        return $invoice;
    }

    public function toInvoiceDetails(Invoice $invoice): void
    {
        $details = new InvoiceDetail;
        $attributes = $this->attributeAssembly->getComprobanteAtributos();

        $details->invoice_id = $invoice->id;
        $details->version = $attributes->getVersion();
        $details->serie = $attributes->getSerie();
        $details->folio = $attributes->getFolio();
        $details->fecha = $attributes->getFecha();
        $details->forma_pago = $attributes->getFormaPago();
        $details->condiciones_de_pago = $attributes->getCondicionesDePago();
        $details->sub_total = $attributes->getSubTotal();
        $details->descuento = $attributes->getDescuento();
        $details->moneda = $attributes->getMoneda();
        $details->tipo_cambio = $attributes->getTipoCambio();
        $details->total = $attributes->getTotal();
        $details->tipo_de_comprobante = $attributes->getTipoDeComprobante();
        $details->exportacion = $attributes->getExportacion();
        $details->metodo_pago = $attributes->getMetodoPago();
        $details->lugar_expedicion = $attributes->getLugarExpedicion();
        $details->receptor_rfc = $this->attributeAssembly->getReceptorAtributos()->getRfc();
        $details->save();
    }

    public function toInvoiceBalances(Invoice $invoice): void
    {
        $concepts = $this->attributeAssembly->getConceptos();
        $localTax = $this->attributeAssembly->getComplementoImpuestosLocales();
        $balance = new InvoiceBalance;

        $balance->invoice_id = $invoice->id;
        $balance->gross_sub_total = $concepts->sum('gross_sub_total');
        $balance->sub_total = $concepts->sum('sub_total');
        $balance->discount = $concepts->sum('discount');
        $balance->tax = $concepts->sum('tax');
        $balance->total = $concepts->sum('total');
        $balance->local_tax = $localTax->sum('amount');
        $balance->balance_total = null; // TODO: ES LA RESTA DEL IMPUESTO LOCAL PERO NO SE CONTRA QUE SI TOTAL O SUBTOTAL.
        $balance->is_paid = ComprobanteDeIngresoRuleHelper::getIsPaid($this->attributeAssembly->getComprobanteAtributos()->getMetodoPago());
        $balance->invoice_payment_type_id = ComprobanteDeIngresoRuleHelper::getPaymentTypeId($this->attributeAssembly->getComprobanteAtributos()->getMetodoPago());
        $balance->save();
    }

    public function toInvoiceTaxes(Invoice $invoice): void
    {
        $concepts = $this->attributeAssembly->getConceptos();
        $tax = new InvoiceTax;

        $tax->invoice_id = $invoice->id;
        $tax->total_impuestos_retenidos = $concepts->sum('total_transfer_taxes');
        $tax->total_impuestos_trasladados = $concepts->sum('total_retention_taxes');
        $tax->save();

        $this->saveInvoiceTaxDetails($tax, $concepts);
    }

    private function saveInvoiceTaxDetails(InvoiceTax $invoiceTax, Collection $concepts): void
    {
        $concepts->each(function (Collection $concept) use ($invoiceTax) {
            $this->putRegisterTax($invoiceTax, $concept->get('conceptSat'));
        });
    }

    private function putRegisterTax(InvoiceTax $invoiceTax, ConceptoAtributos $conceptoAtributos): void
    {
        $conceptoAtributos->getImpuestoTraslados()->each(function (ImpuestoTrasladoAtributos $concept) use ($invoiceTax) {
            $this->saveTax($invoiceTax, $concept->getCollection(), InvoiceTaxTypeEnum::TRASLADO);
        });

        $conceptoAtributos->getImpuestoRetenidos()->each(function (ImpuestoRetenidoAtributos $concept) use ($invoiceTax) {
            $this->saveTax($invoiceTax, $concept->getCollection(), InvoiceTaxTypeEnum::RETENCION);
        });
    }

    private function saveTax(InvoiceTax $invoiceTax, Collection $collect, InvoiceTaxTypeEnum $enum): void
    {
        $data = new InvoiceTaxDetail;

        $data->invoice_tax_id = $invoiceTax->id;
        $data->invoice_tax_type_id = $enum->value;
        $data->base = $collect->get('Base');
        $data->impuesto = $collect->get('Impuesto');
        $data->tipo_factor = $collect->get('TipoFactor');
        $data->tasa_o_cuota = $collect->get('TasaOCuota');
        $data->importe = $collect->get('Importe');
        $data->save();
    }
}
