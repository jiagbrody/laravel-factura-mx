<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\EgresoHandler;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\IngresoHandler;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\PagoHandler;

interface ComprobanteCfdiInterface
{
    public function ingreso(): IngresoHandler;

    public function egreso(): EgresoHandler;

    // public function traslado();

    // public function recepcionDePagos(): PagoHandler;

    // public function retencionesEInformacionDePagos();
}
