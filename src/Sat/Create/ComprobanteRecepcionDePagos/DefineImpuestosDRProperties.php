<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos;

use App\Enums\InvoiceTaxTypeEnum;

class DefineImpuestosDRProperties
{
    private const VALUE_OBJECT_IMP_WITH_TAX = '02';

    public const IMPUESTO_DR = '002';

    public string $BaseDR;

    public string $ImpuestoDR;

    public string $TipoFactorDR;

    public string $TasaOCuotaDR;

    public string $ImporteDR;

    public function setBaseDR($BaseDR): void
    {
        $this->BaseDR = $BaseDR;
    }

    public function getBaseDR(): string
    {
        return $this->BaseDR;
    }

    public function setImpuestoDR(string $ImpuestoDR): void
    {
        $this->ImpuestoDR = $ImpuestoDR;
    }

    public function getImpuestoDR(): string
    {
        return $this->ImpuestoDR;
    }

    public function setTipoFactorDR(string $TipoFactorDR): void
    {
        $this->TipoFactorDR = $TipoFactorDR;
    }

    public function getTipoFactorDR(): string
    {
        return $this->TipoFactorDR;
    }

    public function setTasaOCuotaDR(string $TasaOCuotaDR): void
    {
        $this->TasaOCuotaDR = $TasaOCuotaDR;
    }

    public function getTasaOCuotaDR(): string
    {
        return $this->TasaOCuotaDR;
    }

    public function setImporteDR($ImporteDR): void
    {
        $this->ImporteDR = $ImporteDR;
    }

    public function getImporteDR(): string
    {
        return $this->ImporteDR;
    }

    // public function getImpuestosDRTraslados(Invoice $invoice, DefineDoctoRelacionadoProperties $doctoR): \Illuminate\Support\Collection
    // {
    //     $traslados = $invoice->invoiceTax?->invoiceTaxDetails->where('invoice_tax_type_id', InvoiceTaxTypeEnum::TRASLADO->value);
    //
    //     if ($traslados) {
    //         $doctoR->ObjetoImpDR = self::VALUE_OBJECT_IMP_WITH_TAX;
    //         // TODO: SACAR DE LO QUE QUEDE PENDIENTE.
    //         $percentajePay = floatval($doctoR->ImpPagado) / $invoice->invoiceDetail->total;
    //
    //         $collect = collect();
    //         foreach ($traslados as $traslado) {
    //             // TODO: SACAR DE LO QUE QUEDE PENDIENTE.
    //             $baseDR = ($traslado->base * $percentajePay);
    //
    //             $this->BaseDR = PatronDeDatosHelper::t_import($baseDR);
    //             $this->ImpuestoDR = $traslado->impuesto;
    //             $this->TipoFactorDR = $traslado->tipo_factor;
    //             if ($traslado->tasa_o_cuota) {
    //                 $this->TasaOCuotaDR = $traslado->tasa_o_cuota;
    //             }
    //             if ($traslado->importe) {
    //                 $this->ImporteDR = PatronDeDatosHelper::t_import(($baseDR * $traslado->tasa_o_cuota));
    //             }
    //
    //             $collect->push($this);
    //         }
    //
    //         return $collect;
    //     }
    //
    //     return collect();
    // }
    //
    // public function getImpuestosDRRetenciones(Invoice $invoice, DefineDoctoRelacionadoProperties $doctoR): \Illuminate\Support\Collection
    // {
    //     $retenciones = $invoice->invoiceTax?->invoiceTaxDetails->where('invoice_tax_type_id',
    //         InvoiceTaxTypeEnum::RETENCION->value);
    //
    //     if ($retenciones) {
    //         $doctoR->ObjetoImpDR = self::VALUE_OBJECT_IMP_WITH_TAX;
    //
    //         // TODO: SACAR DE LO QUE QUEDE PENDIENTE.
    //         $percentajePay = floatval($doctoR->ImpPagado) / $invoice->invoiceDetail->total;
    //
    //         $collect = collect();
    //         foreach ($retenciones as $retencion) {
    //             // TODO: SACAR DE LO QUE QUEDE PENDIENTE.
    //             $baseDR = $retencion->base * $percentajePay;
    //
    //             $this->BaseDR = $baseDR;
    //             $this->ImpuestoDR = $retencion->impuesto;
    //             $this->TipoFactorDR = $retencion->tipo_factor;
    //             $this->TasaOCuotaDR = $retencion->tasa_o_cuota;
    //             $this->ImporteDR = ($baseDR * $retencion->tasa_o_cuota);
    //
    //             $collect->push($this);
    //         }
    //
    //         return $collect;
    //     }
    //
    //     return collect();
    // }
}
