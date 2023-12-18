<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso\EgresoHandler;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso\IngresoHandler;

interface ComprobanteCfdiInterface
{
    public function ingreso(): IngresoHandler;

    public function egreso(): EgresoHandler;

    // public function traslado();

    // public function recepcionDePagos(): PagoHandler;

    // public function retencionesEInformacionDePagos();
}
