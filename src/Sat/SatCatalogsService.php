<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

final class SatCatalogsService
{
    private static function connection(string $tableName): Builder
    {
        return DB::connection('sqlite-sat-catalogs')->table($tableName);
    }

    public static function getClaveUnidad(array $filterByIds = [])
    {
        $claveUnidadesQuery = self::connection('cfdi_40_claves_unidades');
        if (! empty($filterByIds)) {
            $claveUnidadesQuery->whereIn('id', $filterByIds);
        }
        $claveUnidades = $claveUnidadesQuery->get();

        return $claveUnidades->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getRegimenFiscal(): Collection
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

    public static function getUsoCfdi(null|string|array $hasId = null): Collection|stdClass
    {
        $usosCfdiQuery = self::connection('cfdi_40_usos_cfdi');
        if ($hasId !== null) {
            if (is_string($hasId)) {
                return $usosCfdiQuery->first();
            }
            $usosCfdi = self::connection('cfdi_40_usos_cfdi')
                ->where('id', '=', $hasId)
                ->get();
        } else {
            $usosCfdi = $usosCfdiQuery->get();
        }

        return $usosCfdi->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getMetodoPago(array $filterByIds = []): Collection
    {
        $query = self::connection('cfdi_40_metodos_pago');
        if (! empty($filterByIds)) {
            $query->whereIn('id', $filterByIds);
        }

        return $query->get()->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getPais(): Collection
    {
        $metodoPago = self::connection('cfdi_40_paises')->get();

        return $metodoPago->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getMoneda(array $filterByIds = []): Collection
    {
        $query = self::connection('cfdi_40_monedas');
        if (! empty($filterByIds)) {
            $query->whereIn('id', $filterByIds);
        }

        return $query->get()->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getFormaPago(array $filterByIds = []): Collection
    {
        $query = self::connection('cfdi_40_formas_pago');
        if (! empty($filterByIds)) {
            $query->whereIn('id', $filterByIds);
        }

        return $query->get()->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getExportacion(array $filterByIds = []): Collection
    {
        $query = self::connection('cfdi_40_exportaciones');
        if (! empty($filterByIds)) {
            $query->whereIn('id', $filterByIds);
        }

        return $query->get()->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getTipoRelacion(): Collection
    {
        $tipoRelacion = self::connection('cfdi_40_tipos_relaciones')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getObjetoImpuesto(): Collection
    {
        $tipoRelacion = self::connection('cfdi_40_objetos_impuestos')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getImpuesto(): Collection
    {
        $tipoRelacion = self::connection('cfdi_40_impuestos')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    public static function getTipoFactor(): Collection
    {
        $tipoRelacion = self::connection('cfdi_40_tipos_factores')->get();

        return $tipoRelacion->each(function ($item) {
            $item->name = $item->id;
        });
    }

    public static function getPagosTipoCadenaPago(array $filterByIds = [])
    {
        $query = self::connection('pagos_tipos_cadena_pago');
        if (! empty($filterByIds)) {
            $query->whereIn('id', $filterByIds);
        }

        return $query->get()->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }

    /*
    [
      {
        "previouslyCapturedInvoices": [
          {
            "statement_invoice_id": 24,
            "amount": 102
          },
          {
            "statement_invoice_id": 219,
            "amount": 102
          },
          {
            "statement_invoice_id": 2901,
            "amount": 102
          },
        ],
      },
      {
        "previouslyCapturedInvoices": [
          {
            "statement_invoice_id": 12,
            "amount": 102
          },
          {
            "statement_invoice_id": 992,
            "amount": 102
          },
          {
            "statement_invoice_id": 21,
            "amount": 102
          },
        ],
      }
    ]
     * */

    /*
     * There is no filter by ID because it does not exist in the SAT catalogs.
     */
    public static function getTasaOCuota(): Collection
    {
        $query = self::connection('cfdi_40_reglas_tasa_cuota')->get();

        return $query->each(function ($item) {
            $item->id = $item->valor;
            $item->name = $item->tipo.' - '.$item->valor.' - '.$item->factor.' - '.$item->impuesto;
        });
    }

    public static function getTipoDeComprobante(array $filterByIds = []): Collection
    {
        $query = self::connection('cfdi_40_tipos_comprobantes');
        if (! empty($filterByIds)) {
            $query->whereIn('id', $filterByIds);
        }

        return $query->get()->each(function ($item) {
            $item->name = $item->id.' - '.$item->texto;
        });
    }
}
