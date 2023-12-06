<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\IngresoHandler;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\PagoHandler;

interface ComprobanteCfdiInterface
{
    public function ingreso(): IngresoHandler;

    public function egreso();

    public function traslado();

    public function recepcionDePagos(): PagoHandler;

    public function retencionesEInformacionDePagos();
}
