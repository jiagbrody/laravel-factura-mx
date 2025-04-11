<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Models\Invoice;

interface SaveIngresoInterface
{
    /*
     * TODO: ESTE METODO DEBERIA DE IR EN OTRA INTERFACE LA CUAL SE USARA EN OTRAS CREACIONES DE FACTURAS.
     */
    /*
     * CREAR UNICO REGISTRO DE FACTURA QUE NO ES POSIBLE BORRAR.
     */
    public function createNewInvoice(int $companyHelperId): Invoice;

    /*
     * TODO: ESTE METODO DEBERIA DE IR EN OTRA INTERFACE LA CUAL SE USARA EN OTRAS CREACIONES DE FACTURAS.
     */
    /*
     * GUARDAR COMPLEMENTO "CfdiRelacionados" DE LA FACTURA O MODIFICAR BORRADOR CON ERRORES DE TIMBRADO.
     */
    public function upsertRelationshipsAddOn(Invoice $invoice, Collection $cfdiRelationships);

    /*
     * GUARDAR TABLAS ADICIONALES DE LA FACTURA O MODIFICAR BORRADOR CON ERRORES DE TIMBRADO.
     */
    public function upsertAdditionalTables(Invoice $invoice);

    // public function toInvoiceBalances(Invoice $invoice);

    // public function toInvoiceTaxes(Invoice $invoice);

    // public function ToComplementLocalTax(Invoice $invoice, Collection $localTaxes);
}
