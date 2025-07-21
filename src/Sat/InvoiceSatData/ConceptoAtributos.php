<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

use Illuminate\Support\Collection;

/*
 * JSON EXAMPLE:
 *

"Conceptos": [
        {
            "ClaveProdServ": "84111506",
            "NoIdentificacion": "1",
            "Cantidad": "1",
            "ClaveUnidad": "ACT",
            "Unidad": "ACT",
            "Descripcion": "84111506 DESCUENTOS Y BONIFICACIONES GAS NATURAL",
            "ValorUnitario": "1308.35",
            "Importe": "1308.35",
            "Descuento": "0.00",
            "ObjetoImp": "02",
            "Impuestos": {
            "Traslados": [
                    {
                        "Base": "1308.350000",
                        "Importe": "209.34",
                        "Impuesto": "002",
                        "TasaOCuota": "0.160000",
                        "TipoFactor": "Tasa"
                    }
                ],
            }
        }
    ]
*/

final readonly class ConceptoAtributos
{
    use AtributosHelperTrait;

    const RELATED_NAME_CONCEPTO_TRASLADO = 'impuestoTraslados';

    const RELATED_NAME_CONCEPTO_RETENIDO = 'impuestoRetenidos';

    public string $ClaveProdServ;

    public string $NoIdentificacion;

    public string $Cantidad;

    public string $ClaveUnidad;

    public string $Unidad;

    public string $Descripcion;

    public string $ValorUnitario;

    public string $Importe;

    public string $Descuento;

    public string $ObjetoImp;

    public Collection $impuestoTraslados;

    public Collection $impuestoRetenidos;

    public function __construct()
    {
        $this->impuestoTraslados = collect();
        $this->impuestoRetenidos = collect();
    }

    public function getOnlySimplePropertiesCollection(): Collection
    {
        $collect = $this->getCollection();
        $collect->forget(self::RELATED_NAME_CONCEPTO_TRASLADO);
        $collect->forget(self::RELATED_NAME_CONCEPTO_RETENIDO);

        return $collect;
    }

    public function setClaveProdServ(string $ClaveProdServ): void
    {
        $this->ClaveProdServ = $ClaveProdServ;
    }

    public function getClaveProdServ(): string
    {
        return $this->ClaveProdServ;
    }

    public function setCantidad(string|int|float $Cantidad): void
    {
        if (is_float($Cantidad)) {
            $this->Cantidad = PatronDeDatosHelper::t_import($Cantidad);
        } else {
            $this->Cantidad = (string) $Cantidad;
        }
    }

    public function getCantidad(): string
    {
        return $this->Cantidad;
    }

    public function setClaveUnidad(string $ClaveUnidad): void
    {
        $this->ClaveUnidad = $ClaveUnidad;
    }

    public function setNoIdentificacion(string $NoIdentificacion): void
    {
        $this->NoIdentificacion = $NoIdentificacion;
    }

    public function getNoIdentificacion(): string
    {
        return $this->NoIdentificacion;
    }

    public function setUnidad(string $Unidad): void
    {
        $this->Unidad = $Unidad;
    }

    public function setDescripcion(string $Descripcion): void
    {
        $this->Descripcion = $Descripcion;
    }

    public function getDescripcion(): string
    {
        return $this->Descripcion;
    }

    public function setValorUnitario(string|int|float $ValorUnitario): void
    {
        if (is_float($ValorUnitario)) {
            $this->ValorUnitario = PatronDeDatosHelper::t_import($ValorUnitario);
        } else {
            $this->ValorUnitario = (string) $ValorUnitario;
        }
    }

    public function getValorUnitario(): string
    {
        return $this->ValorUnitario;
    }

    public function getClaveUnidad(): string
    {
        return $this->ClaveUnidad;
    }

    public function getUnidad(): string
    {
        return $this->Unidad;
    }

    public function setImporte(string|int|float $Importe): void
    {
        if (is_float($Importe)) {
            $this->Importe = (string) PatronDeDatosHelper::t_import($Importe);
        } else {
            $this->Importe = (string) $Importe;
        }
    }

    public function getImporte(): string
    {
        return $this->Importe;
    }

    public function setDescuento(float $Descuento): void
    {
        $this->Descuento = (string) PatronDeDatosHelper::t_import($Descuento);
    }

    public function getDescuento(): string
    {
        return $this->Descuento;
    }

    public function setObjetoImp(string $ObjetoImp): void
    {
        $this->ObjetoImp = $ObjetoImp;
    }

    public function getObjetoImp(): string
    {
        return $this->ObjetoImp;
    }

    public function addImpuestoTraslado(ImpuestoTrasladoAtributos $impuestoTrasladoAtributos): void
    {
        $this->impuestoTraslados->push($impuestoTrasladoAtributos);
    }

    public function getImpuestoTraslados(): Collection
    {
        return $this->impuestoTraslados;
    }

    public function getSumImporteImpuestoTraslados(): float
    {
        // $importesDeTraslado = $this->impuestoTraslados->map(function (ImpuestoTrasladoAtributos $item) {
        //     return (float) $item->getImporte();
        // });

        return $this->impuestoTraslados->sum('Importe');
    }

    public function addImpuestoRetenido(ImpuestoRetenidoAtributos $impuestoRetenidoAtributos): void
    {
        $this->impuestoRetenidos->push($impuestoRetenidoAtributos);
    }

    public function getImpuestoRetenidos(): Collection
    {
        return $this->impuestoRetenidos;
    }
}
