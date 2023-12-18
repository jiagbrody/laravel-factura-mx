<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create;

use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso\EgresoHandler;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso\IngresoHandler;

interface ComprobanteInterface
{
    public function ingreso(): IngresoHandler;

    public function egreso(): EgresoHandler;
}
