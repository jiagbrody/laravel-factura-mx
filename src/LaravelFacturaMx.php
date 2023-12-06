<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx;

use JiagBrody\LaravelFacturaMx\Sat\ComprobanteCfdiInterface;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\EgresoHandler;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\IngresoHandler;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\PagoHandler;

class LaravelFacturaMx implements ComprobanteCfdiInterface
{
    public function testing(): string
    {
        return 'test';
    }

    public function ingreso(): IngresoHandler
    {
        return new IngresoHandler();
    }

    public function egreso(): EgresoHandler
    {
        return new EgresoHandler();
    }

    public function recepcionDePagos(): PagoHandler
    {
        return new PagoHandler;
    }

    public function traslado()
    {
        // TODO: Implement traslado() method.
    }

    public function retencionesEInformacionDePagos()
    {
        // TODO: Implement retencionesEInformacionDePagos() method.
    }
}
