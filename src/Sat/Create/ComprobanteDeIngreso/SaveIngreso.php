<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTaxTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceBalance;
use JiagBrody\LaravelFacturaMx\Models\InvoiceComplementLocalTax;
use JiagBrody\LaravelFacturaMx\Models\InvoiceComplementLocalTaxDetail;
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

    public function toInvoiceDetail(Invoice $invoice): void
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
        $balance = new InvoiceBalance;

        $balance->invoice_id = $invoice->id;
        $balance->gross_sub_total = $concepts->sum('gross_sub_total');
        $balance->sub_total = $concepts->sum('sub_total');
        $balance->discount = $concepts->sum('discount');
        $balance->tax = $concepts->sum('tax');
        $balance->total = $concepts->sum('total');
        $balance->final_total_balance = null; // TODO: ES LA RESTA DEL IMPUESTO LOCAL PERO NO SE CONTRA QUE SI TOTAL O SUBTOTAL.
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

    public function ToComplementLocalTax(Invoice $invoice, Collection $localTaxes): void
    {
        if ($localTaxes->isNotEmpty()) {
            $complementLocalTax = new InvoiceComplementLocalTax;
            $complementLocalTax->invoice_id = $invoice->id;
            $complementLocalTax->total_de_retenciones = $localTaxes->where('invoice_tax_type_id', InvoiceTaxTypeEnum::RETENCION->value)->sum('Importe');
            $complementLocalTax->total_de_traslados = $localTaxes->where('invoice_tax_type_id', InvoiceTaxTypeEnum::TRASLADO->value)->sum('Importe');
            $complementLocalTax->save();

            foreach ($localTaxes as $localTax) {
                $localTaxDetail = new InvoiceComplementLocalTaxDetail;
                $localTaxDetail->invoice_complement_local_tax_id = $complementLocalTax->id;
                $localTaxDetail->invoice_tax_type_id = $localTax['invoice_tax_type_id'];

                switch ($localTax['invoice_tax_type_id']) {
                    case InvoiceTaxTypeEnum::RETENCION->value:
                        $localTaxDetail->imp_loc_retenido = $localTax['ImpLocRetenido'];
                        $localTaxDetail->tasa_de_retencion = $localTax['TasadeRetencion'];
                        break;
                    case InvoiceTaxTypeEnum::TRASLADO->value:
                        $localTaxDetail->imp_loc_trasladado = $localTax['ImpLocTrasladado'];
                        $localTaxDetail->tasa_de_traslado = $localTax['TasadeTraslado'];
                        break;
                }

                $localTaxDetail->importe = $localTax['Importe'];
                $localTaxDetail->save();
            }
        }
    }

    public function toRelatedConcepts(Invoice $invoice): void
    {
        $concepts = $this->attributeAssembly->getConceptos();
        $format = $concepts->groupBy('statement_detail_id')->mapWithKeys(function ($items, $key) {
            $item = $items->first();
            $array = [
                'quantity' => $item['quantity'],
                'unit_price' => $item['price_unit'],
                'gross_sub_total' => $item['gross_sub_total'],
                'discount' => $item['discount'],
                'sub_total' => $item['sub_total'],
                'tax' => $item['tax'],
                'total' => $item['total'],
            ];

            return [$key => $array];
        });

        $invoice->relatedConcepts()->attach($format);
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
