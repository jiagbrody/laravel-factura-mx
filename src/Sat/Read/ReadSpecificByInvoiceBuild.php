<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Read;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Services\BusinessModelConcept\IngresoRelatedBusinessItemsService;
use JiagBrody\LaravelFacturaMx\Services\DataBase\DatabaseService;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

class ReadSpecificByInvoiceBuild
{
    protected Invoice $invoice;

    public readonly DocumentService $documentService;

    public readonly IngresoRelatedBusinessItemsService $ingresoRelatedBusinessItemsService;

    public readonly DatabaseService $databaseService;

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;

        // LIBRERIA EXTERNA PARA OBTENER INFORMACION DE LOS DOCUMENTOS (SE USA EN VARIAS PARTES DE LA LIBRERIA)
        $this->documentService = new DocumentService;
        $this->documentService->setInvoice(invoice: $invoice);

        // OBTENER INFORMACIÓN REFERENTE A LOS ITEMS DEL MODELO DE NEGOCIO DE LA LIBRERIA DESDE EL CLIENTE, PARA LAS FACTURAS DE INGRESO.
        $this->ingresoRelatedBusinessItemsService = new IngresoRelatedBusinessItemsService(invoice: $invoice);

        // OBTENER INFORMACIÓN DE LA LIBRERIA DESDE EL CLIENTE
        $this->databaseService = new DatabaseService;
        $this->databaseService->setInvoice(invoice: $invoice);
    }
}
