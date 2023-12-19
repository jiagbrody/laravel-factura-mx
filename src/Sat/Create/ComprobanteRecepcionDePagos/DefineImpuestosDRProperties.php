<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos;

use App\Enums\InvoiceTaxTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\PatronDeDatosHelper;

class DefineImpuestosDRProperties
{
    private const VALUE_OBJECT_IMP_WITH_TAX = '02';

    public float $BaseDR;

    public string $ImpuestoDR;

    public string $TipoFactorDR;

    public string $TasaOCuotaDR;

    public float $ImporteDR;

    public function getImpuestosDRTraslados(Invoice $invoice, DefineDoctoRelacionadoProperties $doctoR): \Illuminate\Support\Collection
    {
        $traslados = $invoice->invoiceTax?->invoiceTaxDetails->where('invoice_tax_type_id', InvoiceTaxTypeEnum::TRASLADO->value);

        if ($traslados) {
            $doctoR->ObjetoImpDR = self::VALUE_OBJECT_IMP_WITH_TAX;
            //TODO: SACAR DE LO QUE QUEDE PENDIENTE.
            $percentajePay = floatval($doctoR->ImpPagado) / $invoice->invoiceDetail->total;

            $collect = collect();
            foreach ($traslados as $traslado) {
                //TODO: SACAR DE LO QUE QUEDE PENDIENTE.
                $baseDR = ($traslado->base * $percentajePay);

                $this->BaseDR = PatronDeDatosHelper::t_import($baseDR);
                $this->ImpuestoDR = $traslado->impuesto;
                $this->TipoFactorDR = $traslado->tipo_factor;
                if ($traslado->tasa_o_cuota) {
                    $this->TasaOCuotaDR = $traslado->tasa_o_cuota;
                }
                if ($traslado->importe) {
                    $this->ImporteDR = PatronDeDatosHelper::t_import(($baseDR * $traslado->tasa_o_cuota));
                }

                $collect->push($this);
            }

            return $collect;
        }

        return collect();
    }

    public function getImpuestosDRRetenciones(Invoice $invoice, DefineDoctoRelacionadoProperties $doctoR): \Illuminate\Support\Collection
    {
        $retenciones = $invoice->invoiceTax?->invoiceTaxDetails->where('invoice_tax_type_id',
            InvoiceTaxTypeEnum::RETENCION->value);

        if ($retenciones) {
            $doctoR->ObjetoImpDR = self::VALUE_OBJECT_IMP_WITH_TAX;

            //TODO: SACAR DE LO QUE QUEDE PENDIENTE.
            $percentajePay = floatval($doctoR->ImpPagado) / $invoice->invoiceDetail->total;

            $collect = collect();
            foreach ($retenciones as $retencion) {
                //TODO: SACAR DE LO QUE QUEDE PENDIENTE.
                $baseDR = $retencion->base * $percentajePay;

                $this->BaseDR = $baseDR;
                $this->ImpuestoDR = $retencion->impuesto;
                $this->TipoFactorDR = $retencion->tipo_factor;
                $this->TasaOCuotaDR = $retencion->tasa_o_cuota;
                $this->ImporteDR = ($baseDR * $retencion->tasa_o_cuota);

                $collect->push($this);
            }

            return $collect;
        }

        return collect();
    }
}
