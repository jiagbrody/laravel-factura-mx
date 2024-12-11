<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Read;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Services\BusinessModelConcept\IngresoRelatedBusinessItemsService;
use JiagBrody\LaravelFacturaMx\Services\DataBase\DatabaseService;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

class ReadInvoiceBuilder
{
    public readonly DocumentService $documentService;

    public readonly IngresoRelatedBusinessItemsService $ingresoRelatedBusinessItemsService;

    public readonly DatabaseService $databaseService;

    public function __construct(protected Invoice $invoice)
    {
        //LIBRERIA EXTERNA PARA OBTENER INFORMACION DE LOS DOCUMENTOS (SE USA EN VARIAS PARTES DE LA LIBRERIA)
        $this->documentService = new DocumentService;
        $this->documentService->setInvoice($this->invoice);

        //OBTENER INFORMACIÓN REFERENTE A LOS ITEMS DEL MODELO DE NEGOCIO DE LA LIBRERIA DESDE EL CLIENTE
        $this->ingresoRelatedBusinessItemsService = new IngresoRelatedBusinessItemsService(invoice: $this->invoice);

        //OBTENER INFORMACIÓN DE LA LIBRERIA DESDE EL CLIENTE
        $this->databaseService = new DatabaseService(invoice: $this->invoice);
    }
}
