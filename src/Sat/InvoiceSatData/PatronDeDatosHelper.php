<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

use Carbon\Carbon;

final class PatronDeDatosHelper
{
    static function t_ImporteMXN($value): float
    {
        return round($value, 2);
    }

    /*
     * Usado en los importes del "complemento de Impuestos locales"
     */
    static function t_import_custom($value, $decimals): float
    {
        return round($value, $decimals);
    }

    static function t_import($value): float
    {
        return round($value, 6);
    }

    static function t_FechaH(Carbon $date): string
    {
        return $date->format('Y-m-d\TH:i:s');
    }
}
