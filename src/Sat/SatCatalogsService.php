<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use Illuminate\Support\Facades\DB;

final class SatCatalogsService
{
    private static function connection(string $tableName): \Illuminate\Database\Query\Builder
    {
        return DB::connection('sqlite-sat-catalogs')->table($tableName);
    }

    public static function getClaveUnidad()
    {
        $claveUnidades = self::connection('cfdi_40_claves_unidades')->get();

        return $claveUnidades->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getRegimenFiscal(): \Illuminate\Support\Collection
    {
        $regimenFiscales = self::connection('cfdi_40_regimenes_fiscales')->get();

        return $regimenFiscales->each(function ($item) {
            $text = '';
            switch ($item) {
                case ($item->aplica_fisica == '1') && ($item->aplica_moral == '1'):
                    $text = 'persona fisica y moral';
                    break;
                case $item->aplica_fisica == '1':
                    $text = 'persona fisica';
                    break;
                case $item->aplica_moral == '1':
                    $text = 'persona moral';
                    break;
            }

            $item->name = $item->id.' - '.$item->texto.' - '.$text;
        });
    }

    public static function getUsoCfdi(): \Illuminate\Support\Collection
    {
        if (! request()->has('id')) {
            abort(422, 'No se ha proporcionado un ID valido del regimen fiscal');
        }

        $regimenFiscal = self::connection('cfdi_40_regimenes_fiscales')->find(request()->get('id'));

        $usosCfdi = self::connection('cfdi_40_usos_cfdi')
            ->where('regimenes_fiscales_receptores', 'LIKE', '%'.$regimenFiscal->id.'%')
            ->get();

        return $usosCfdi->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getMetodoPago(): \Illuminate\Support\Collection
    {
        $metodoPago = self::connection('cfdi_40_metodos_pago')->get();

        return $metodoPago->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getPais(): \Illuminate\Support\Collection
    {
        $metodoPago = self::connection('cfdi_40_paises')->get();

        return $metodoPago->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getMoneda(): \Illuminate\Support\Collection
    {
        $moneda = self::connection('cfdi_40_monedas')->whereIn('id', ['MXN', 'USD'])->get();

        return $moneda->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getFormaPago(array $filterByIds = []): \Illuminate\Support\Collection
    {
        $query = self::connection('cfdi_40_formas_pago');
        if (! empty($filterByIds)) {
            $query->whereIn('id', $filterByIds);
        }

        return $query->get()->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getExportacion(): \Illuminate\Support\Collection
    {
        $formaPago = self::connection('cfdi_40_exportaciones')->get();

        return $formaPago->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getTipoRelacion(): \Illuminate\Support\Collection
    {
        $tipoRelacion = self::connection('cfdi_40_tipos_relaciones')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getObjetoImpuesto(): \Illuminate\Support\Collection
    {
        $tipoRelacion = self::connection('cfdi_40_objetos_impuestos')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getImpuesto(): \Illuminate\Support\Collection
    {
        $tipoRelacion = self::connection('cfdi_40_impuestos')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getTipoFactor(): \Illuminate\Support\Collection
    {
        $tipoRelacion = self::connection('cfdi_40_tipos_factores')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id;
        });
    }

    public static function getTasaOCuota(): \Illuminate\Support\Collection
    {
        $tipoRelacion = self::connection('cfdi_40_reglas_tasa_cuota')->get();

        return $tipoRelacion->each(function ($item) {
            $item->id = $item->valor;
            $item->name = $item->tipo.' - '.$item->valor.' - '.$item->factor.' - '.$item->impuesto;
        });
    }

    public static function getTipoDeComprobante(): \Illuminate\Support\Collection
    {
        $tipoRelacion = self::connection('cfdi_40_tipos_comprobantes')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }
}
