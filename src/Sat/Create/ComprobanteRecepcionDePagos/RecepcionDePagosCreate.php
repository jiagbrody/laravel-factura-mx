<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos;

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\Create\Helpers\GenericCreator;

class RecepcionDePagosCreate implements CfdiHandlerInterface
{
    public function custom(InvoiceCompany $company): PagoCreator
    {
        return new PagoCreator($company);
    }
}
