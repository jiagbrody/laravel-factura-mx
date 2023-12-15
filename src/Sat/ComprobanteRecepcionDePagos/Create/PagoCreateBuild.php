<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\Create;

use App\Enums\InvoiceTaxTypeEnum;
use App\Helpers\Cfdi\ConvertXmlContentToObjectHelper;
use App\Models\Cfdi;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use CfdiUtils\CfdiCreator40;
use JiagBrody\LaravelFacturaMx\Sat\Helper\DraftBuild;
use JiagBrody\LaravelFacturaMx\Sat\Helper\GenerateInvoicePdfFileHelperTrait;
use JiagBrody\LaravelFacturaMx\Sat\Helper\XmlFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use PhpCfdi\Credentials\Credential;

class PagoCreateBuild extends DraftBuild
{
    use GenerateInvoicePdfFileHelperTrait;

    protected Invoice $invoice;

    private object $cfdiObject;

    protected bool $asNewRecord;

    public function __construct(protected Credential $credential, protected CfdiCreator40 $creatorCfdi, protected InvoiceCompanyHelper $companyHelper)
    {
    }

    public function setProperties(Invoice $invoice, bool $asNewRecord = false): void
    {
        $this->invoice = $invoice;
        $this->cfdiObject = ConvertXmlContentToObjectHelper::make($this->creatorCfdi->asXml());
        $this->asNewRecord = $asNewRecord;
    }

    public function saveDraft(): void
    {
        $this->saveDraftDetails();

        $this->savePayments();

        // GENERO DOCUMENTOS XML Y EL PDF.
        $xml = (new XmlFileSatHelperBuilder($this->invoice))->generate($this->creatorCfdi->asXml());
        $this->createPdfFile($xml, $this->creatorCfdi->asXml());
    }

    private function savePayments(): void
    {
        // GUARDO TOTALES DEL PAGO
        $this->saveTotales($this->cfdiObject->Complemento->Pagos->Totales);

        if (! $this->asNewRecord) {
            $this->invoice->invoicePayments()->delete();
        }

        foreach ($this->cfdiObject->Complemento->Pagos->Pago as $pago) {

            // BUSCO REGISTROS ANTERIORES DE LOS DOCUMENTOS RELACIONADOS
            $collect = collect($pago->DoctoRelacionado);
            $paymentGroupToDocument = Cfdi::whereIn('uuid', $collect->pluck('IdDocumento'))->get();

            // GUARDO PAGO
            $payment = $this->savePago($pago);

            foreach ($pago->DoctoRelacionado as $doctoRelacionado) {

                // GUARDO DOCUMENTO RELACIONADO
                $cfdi = $paymentGroupToDocument->where('uuid', $doctoRelacionado->IdDocumento)->first();
                $doctoR = $this->saveDoctoRelacionado($payment, $doctoRelacionado, $cfdi);

                // GUARDO IMPUESTOS DEL DOCUMENTO SI EXISTEN
                if (isset($doctoRelacionado->ImpuestosDR)) {
                    if (isset($doctoRelacionado->ImpuestosDR->TrasladosDR->TrasladoDR)) {
                        foreach ($doctoRelacionado->ImpuestosDR->TrasladosDR->TrasladoDR as $traslado) {
                            $data = $doctoR->invoicePaymentDocumentTaxes()->make();
                            // dd($doctoRelacionado->ImpuestosDR->TrasladosDR->TrasladoDR, $data, InvoiceTaxTypeEnum::TRASLADO, $traslado);
                            $this->saveImpuestoDR($data, InvoiceTaxTypeEnum::TRASLADO, $traslado);
                        }
                    }

                    if (isset($doctoRelacionado->ImpuestosDR->RetencionesDR->RetencionDR)) {
                        foreach ($doctoRelacionado->ImpuestosDR->RetencionesDR->RetencionDR as $retencion) {
                            $data = $doctoR->invoicePaymentDocumentTaxes()->make();
                            $this->saveImpuestoDR($data, InvoiceTaxTypeEnum::RETENCION, $retencion);
                        }
                    }
                }
            }

            // GUARDO IMPUESTOS DEL PAGO SI EXISTEN
            if (isset($pago->ImpuestosP)) {
                if (isset($pago->ImpuestosP->TrasladosP->TrasladoP)) {
                    foreach ($pago->ImpuestosP->TrasladosP->TrasladoP as $traslado) {
                        $data = $payment->invoicePaymentaxes()->make();
                        $this->saveImpuestoP($data, InvoiceTaxTypeEnum::TRASLADO, $traslado);
                    }
                }

                if (isset($pago->ImpuestosP->RetencionesP->RetencionP)) {
                    foreach ($pago->ImpuestosP->RetencionesP->RetencionP as $retencion) {
                        $data = $payment->invoicePaymentaxes()->make();
                        $this->saveImpuestoP($data, InvoiceTaxTypeEnum::RETENCION, $retencion);
                    }
                }
            }
        }
    }

    private function saveTotales($totals): void
    {
        $totales = $this->invoice->invoicePaymentTotal()->firstOrNew();

        $totales->total_retenciones_iva = $totals->TotalRetencionesIva ?? null;
        $totales->total_retenciones_isr = $totals->TotalRetencionesIsr ?? null;
        $totales->total_retenciones_ieps = $totals->TotalRetencionesIeps ?? null;
        $totales->total_traslados_base_iva_16 = $totals->TotalTrasladosBaseIVA16 ?? null;
        $totales->total_traslados_impuestos_iva_16 = $totals->TotalTrasladosImpuestoIVA16 ?? null;
        $totales->total_traslados_base_iva_8 = $totals->TotalTrasladosBaseIVA_8 ?? null;
        $totales->total_traslados_impuestos_iva_8 = $totals->TotalTrasladosImpuestoIVA8 ?? null;
        $totales->total_traslados_base_iva_0 = $totals->TotalTrasladosBaseIVA0 ?? null;
        $totales->total_traslados_impuestos_iva_0 = $totals->TotalTrasladosImpuestoIVA0 ?? null;
        $totales->total_traslados_base_iva_exento = $totals->TotalTrasladosBaseIVAExento ?? null;
        $totales->monto_total_pagos = $totals->MontoTotalPagos ?? null;
        $totales->save();
    }

    private function savePago($pay): InvoicePayment
    {
        $payment = $this->invoice->invoicePayments()->make();
        $payment->fecha_pago = $pay->FechaPago;
        $payment->forma_de_pago_p = $pay->FormaDePagoP;
        $payment->moneda_p = $pay->MonedaP;
        $payment->tipo_cambio_p = $pay->TipoCambioP;
        $payment->monto = $pay->Monto;
        $payment->save();

        return $payment;
    }

    private function saveDoctoRelacionado(InvoicePayment $invoicePayment, $doctoR, Cfdi $cfdi)
    {
        $doc = $invoicePayment->invoicePaymentDocuments()->make();

        $doc->cfdi_id = $cfdi->id;
        $doc->is_active = false;
        $doc->equivalencia_dr = $doctoR->EquivalenciaDR;
        $doc->num_parcialidad = $doctoR->NumParcialidad;
        $doc->imp_saldo_ant = $doctoR->ImpSaldoAnt;
        $doc->imp_pagado = $doctoR->ImpPagado;
        $doc->imp_saldo_insoluto = $doctoR->ImpSaldoInsoluto;
        $doc->objeto_imp_dr = $doctoR->ObjetoImpDR;

        $doc->save();

        return $doc;
    }

    private function saveImpuestoDR($register, InvoiceTaxTypeEnum $type, $values): void
    {
        $register->invoice_tax_type_id = $type->value;
        $register->base_dr = $values->BaseDR;
        $register->impuesto_dr = $values->ImpuestoDR;
        $register->tipo_factor_dr = $values->TipoFactorDR;
        $register->tasa_o_cuota_dr = $values->TasaOCuotaDR ?? null;
        $register->importe_dr = $values->ImporteDR ?? null;
        $register->save();
    }

    private function saveImpuestoP($register, InvoiceTaxTypeEnum $type, $values): void
    {
        $register->invoice_tax_type_id = $type->value;
        $register->base_p = $values->BaseP;
        $register->impuesto_p = $values->ImpuestoP;
        $register->tipo_factor_p = $values->TipoFactorP;
        $register->tasa_o_cuota_p = $values->TasaOCuotaP ?? null;
        $register->importe_p = $values->ImporteP ?? null;
        $register->save();
    }
}
