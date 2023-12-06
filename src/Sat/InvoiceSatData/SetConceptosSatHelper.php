<?php

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

trait SetConceptosSatHelper
{
    protected function setConceptosSat(Collection $products): void
    {
        if ($products->count() > 0) {
            $products->load([
                'productSatKey',
                'customProductSatKey' => function ($query) {
                    $query->first();
                },
            ]);

            $products->each(function ($item) {
                $this->creatorCfdi->comprobante()
                    ->addConcepto((array)$this->getConcept($item))
                    ->addTraslado((array)$this->getTraslado($item));
            });
        } elseif ($products->count() === 0 && count($this->xmlCreatedEarlier->Conceptos->Concepto ?? []) > 0) {
            foreach ($this->xmlCreatedEarlier->Conceptos->Concepto as $item) {
                $traslado = [];
                if ($item->Impuestos->Traslados->Traslado[0]) {
                    $impuesto = $item->Impuestos->Traslados->Traslado[0];
                    $traslado = [
                        "Base"       => $impuesto->Base,
                        "Impuesto"   => $impuesto->Impuesto,
                        "TipoFactor" => $impuesto->TipoFactor,
                        "TasaOCuota" => $impuesto->TasaOCuota,
                        "Importe"    => $impuesto->Importe,
                    ];
                }

                $this->creatorCfdi->comprobante()->addConcepto([
                    "ClaveProdServ"    => $item->ClaveProdServ,
                    "NoIdentificacion" => $item->NoIdentificacion,
                    "Cantidad"         => $item->Cantidad,
                    "ClaveUnidad"      => $item->ClaveUnidad,
                    "Descripcion"      => $item->Descripcion,
                    "ValorUnitario"    => $item->ValorUnitario,
                    "Importe"          => $item->Importe,
                    "Descuento"        => $item->Descuento,
                    "ObjetoImp"        => $item->ObjetoImp,
                ])->addTraslado($traslado);
            }
        }
    }

    private function getConcept(Product $product): \App\Services\SAT\InvoiceSatData\ConceptoAtributos
    {
        $sat = $product->customProductSatKey ?? $product->productSatKey;

        $this->satAttributes->conceptoAtributos->ClaveProdServ    = $sat->c_clave_prod_serv;
        $this->satAttributes->conceptoAtributos->NoIdentificacion = (string)$product->statement_detail_id;
        $this->satAttributes->conceptoAtributos->Cantidad         = $product->quantity;
        $this->satAttributes->conceptoAtributos->ClaveUnidad      = $sat->c_clave_unidad;
        unset($this->satAttributes->conceptoAtributos->Unidad);
        $this->satAttributes->conceptoAtributos->Descripcion   = ($product->comments) ? $product->name.' - '.$product->comments : $product->name;
        $this->satAttributes->conceptoAtributos->ValorUnitario = PatronDeDatosHelper::t_import($product->price_unit);
        $this->satAttributes->conceptoAtributos->Importe       = PatronDeDatosHelper::t_import($product->gross_sub_total);
        $this->satAttributes->conceptoAtributos->Descuento     = PatronDeDatosHelper::t_import($product->discount);
        $this->satAttributes->conceptoAtributos->ObjetoImp     = '02';

        return $this->satAttributes->conceptoAtributos;
    }

    private function getTraslado(Product $product): null|\App\Services\SAT\InvoiceSatData\ImpuestoTrasladoAtributos
    {
        $sat = $product->customProductSatKey ?? $product->productSatKey;

        $this->satAttributes->impuestoTrasladoAtributos->Base       = PatronDeDatosHelper::t_import($product->sub_total);
        $this->satAttributes->impuestoTrasladoAtributos->Impuesto   = $sat->c_impuesto;
        $this->satAttributes->impuestoTrasladoAtributos->TipoFactor = $sat->c_tipo_factor;
        $this->satAttributes->impuestoTrasladoAtributos->TasaOCuota = $sat->c_tasa_o_cuota;
        $this->satAttributes->impuestoTrasladoAtributos->Importe    = PatronDeDatosHelper::t_import($product->tax);

        if ($sat->c_tipo_factor === 'Exento') {
            unset($this->satAttributes->impuestoTrasladoAtributos->TasaOCuota, $this->satAttributes->impuestoTrasladoAtributos->Importe);
        }

        return $this->satAttributes->impuestoTrasladoAtributos;
    }
}
