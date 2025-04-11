<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create;

use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso\EgresoCreate;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso\IngresoCreate;

class ComprobanteTypes
{
    public function ingreso(): IngresoCreate
    {
        return new IngresoCreate;
    }

    public function egreso(): EgresoCreate
    {
        return new EgresoCreate;
    }

    // public function recepcionDePagos(): PagoHandler
    // {
    //     return new PagoHandler;
    // }

    // public function traslado()
    // {
    //     // TODO: Implement traslado() method.
    // }

    // public function retencionesEInformacionDePagos()
    // {
    //     // TODO: Implement retencionesEInformacionDePagos() method.
    // }
}
