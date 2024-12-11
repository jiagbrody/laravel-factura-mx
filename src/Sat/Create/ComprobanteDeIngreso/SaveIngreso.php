<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTaxTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Facades\LaravelFacturaMx;
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
    public function __construct(protected AttributeAssembly $attributeAssembly) {}

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
        //WILL ALWAYS BE SAVED
        $invoiceDetail = $invoice->invoiceDetail ?? new InvoiceDetail;
        $attributes = $this->attributeAssembly->getComprobanteAtributos();

        $invoiceDetail->invoice_id = $invoice->id;
        $invoiceDetail->version = $attributes->getVersion();
        $invoiceDetail->serie = $attributes->getSerie();
        $invoiceDetail->folio = $attributes->getFolio();
        $invoiceDetail->fecha = $attributes->getFecha();
        $invoiceDetail->forma_pago = $attributes->getFormaPago();
        $invoiceDetail->condiciones_de_pago = $attributes->getCondicionesDePago();
        $invoiceDetail->sub_total = $attributes->getSubTotal();
        $invoiceDetail->descuento = $attributes->getDescuento();
        $invoiceDetail->moneda = $attributes->getMoneda();
        $invoiceDetail->tipo_cambio = $attributes->getTipoCambio();
        $invoiceDetail->total = $attributes->getTotal();
        $invoiceDetail->tipo_de_comprobante = $attributes->getTipoDeComprobante();
        $invoiceDetail->exportacion = $attributes->getExportacion();
        $invoiceDetail->metodo_pago = $attributes->getMetodoPago();
        $invoiceDetail->lugar_expedicion = $attributes->getLugarExpedicion();
        $invoiceDetail->receptor_rfc = $this->attributeAssembly->getReceptorAtributos()->getRfc();
        $invoiceDetail->save();
    }

    public function toInvoiceBalances(Invoice $invoice): void
    {
        //WILL ALWAYS BE SAVED
        $invoiceBalance = $invoice->invoiceBalance ?? new InvoiceBalance;
        $concepts = $this->attributeAssembly->getConceptos();

        $invoiceBalance->invoice_id = $invoice->id;
        $invoiceBalance->gross_sub_total = $concepts->sum('gross_sub_total');
        $invoiceBalance->sub_total = $concepts->sum('sub_total');
        $invoiceBalance->discount = $concepts->sum('discount');
        $invoiceBalance->tax = $concepts->sum('tax');
        $invoiceBalance->total = $concepts->sum('total');
        $invoiceBalance->final_total_balance = null; // TODO: ES LA RESTA DEL IMPUESTO LOCAL PERO NO SE CONTRA QUE SI TOTAL O SUBTOTAL.
        $invoiceBalance->is_paid = ComprobanteDeIngresoRuleHelper::getIsPaid($this->attributeAssembly->getComprobanteAtributos()->getMetodoPago());
        $invoiceBalance->invoice_payment_type_id = ComprobanteDeIngresoRuleHelper::getPaymentTypeId($this->attributeAssembly->getComprobanteAtributos()->getMetodoPago());
        $invoiceBalance->save();
    }

    public function toInvoiceTaxes(Invoice $invoice): void
    {
        //WILL ALWAYS BE SAVED
        $invoiceTax = $invoice->invoiceTax ?? new InvoiceTax;
        $concepts = $this->attributeAssembly->getConceptos();

        $invoiceTax->invoice_id = $invoice->id;
        $invoiceTax->total_impuestos_retenidos = $concepts->sum('total_retention_taxes');
        $invoiceTax->total_impuestos_trasladados = $concepts->sum('total_transfer_taxes');
        $invoiceTax->save();

        $this->saveInvoiceTaxDetails($invoiceTax, $concepts);
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

        $format = $concepts->mapWithKeys(function ($item, $key) use ($invoice) {
            $infoSatByConcept = $this->getAttributesConceptFromClient($item);
            // dd($infoSatByConcept->getImporte());
            $array = [
                'invoice_id' => $invoice->id,
                'statement_detail_id' => $item->get(config('jiagbrody-laravel-factura-mx.column_names.foreign_id_related_to_concepts')),
                'quantity' => $infoSatByConcept->getCantidad(),
                'unit_price' => $infoSatByConcept->getValorUnitario(),
                'gross_sub_total' => $infoSatByConcept->getImporte(),
                'discount' => $infoSatByConcept->getDescuento(),
                'sub_total' => (float) $infoSatByConcept->getImporte() - (float) $infoSatByConcept->getDescuento(),
                'tax' => $infoSatByConcept->getSumImporteImpuestoTraslados(),
                'total' => (float) $infoSatByConcept->getImporte() - $infoSatByConcept->getSumImporteImpuestoTraslados(),
            ];

            return [$key => $array];
        });

        $facturaMx = LaravelFacturaMx::read($invoice);
        $facturaMx->ingresoRelatedBusinessItemsService->setConceptsByInsert($format->toArray());
    }

    private function saveInvoiceTaxDetails(InvoiceTax $invoiceTax, Collection $concepts): void
    {
        $concepts->each(function (Collection $concept) use ($invoiceTax) {
            $this->putRegisterTax($invoiceTax, $this->getAttributesConceptFromClient($concept));
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

    private function getAttributesConceptFromClient(Collection $collection): ConceptoAtributos
    {
        return $collection->get('conceptSat');
    }
}
