<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Models\Invoice;

interface SaveEgresoInterface
{
    /*
     * GUARDAR TABLAS ADICIONALES DE LA FACTURA O MODIFICAR BORRADOR CON ERRORES DE TIMBRADO.
     */
    public function upsertAdditionalTables(Invoice $invoice);
}
