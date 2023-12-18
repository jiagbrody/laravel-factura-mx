<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create;

use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso\EgresoHandler;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso\IngresoHandler;

class ComprobanteBuilder implements ComprobanteInterface
{
    public function ingreso(): IngresoHandler
    {
        return new IngresoHandler();
    }

    public function egreso(): EgresoHandler
    {
        return new EgresoHandler();
    }
}
