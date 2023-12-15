<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos;

use App\Enums\InvoiceTaxTypeEnum;
use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\PatronDeDatosHelper;

final class GetImpuestosDRHelper
{
    const TIPO_FACTOR_EXENTO = 'Exento';

    public static function traslados(Invoice $invoice, DefineDoctoRelacionadoProperties $documentoR): \Illuminate\Support\Collection
    {
        $traslados = $invoice->invoiceTax?->invoiceTaxDetails->where('invoice_tax_type_id', InvoiceTaxTypeEnum::TRASLADO->value);

        if ($traslados) {
            $documentoR->ObjetoImpDR = '02';
            //TODO: SACAR DE LO QUE QUEDE PENDIENTE.
            $percentajePay = floatval($documentoR->ImpPagado) / $invoice->invoiceDetail->total;

            $collect = collect();
            foreach ($traslados as $traslado) {
                $object = new DefineImpuestosDRProperties;

                //TODO: SACAR DE LO QUE QUEDE PENDIENTE.
                $baseDR = ($traslado->base * $percentajePay);

                $object->BaseDR = PatronDeDatosHelper::t_import($baseDR);
                $object->ImpuestoDR = $traslado->impuesto;
                $object->TipoFactorDR = $traslado->tipo_factor;

                if ($object->TipoFactorDR !== self::TIPO_FACTOR_EXENTO) {
                    $object->TasaOCuotaDR = $traslado->tasa_o_cuota;
                    $object->ImporteDR = PatronDeDatosHelper::t_import(($baseDR * $traslado->tasa_o_cuota));
                }

                $collect->push($object);
            }

            return $collect;
        }

        return collect();
    }
}
