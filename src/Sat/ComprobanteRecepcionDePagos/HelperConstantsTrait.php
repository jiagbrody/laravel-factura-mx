<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos;

trait HelperConstantsTrait
{
    public function KEY_TIPO_CAMBIO(): string
    {
        return 'TipoCambioP';
    }

    public function KEY_MONEDA(): string
    {
        return 'MonedaP';
    }

    public function KEY_FECHA_PAGO(): string
    {
        return 'FechaPago';
    }

    public function VALUE_MONEDA_MXN(): string
    {
        return 'MXN';
    }

    public function DEFAULT_VALUE_OBJECT_IMP(): string
    {
        return '01';
    }

    public function VALUE_OBJECT_IMP_WITH_TAX(): string
    {
        return '02';
    }
}
