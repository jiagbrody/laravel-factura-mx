<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Read;

use JiagBrody\LaravelFacturaMx\Services\DataBase\DatabaseService;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

class ReadInvoiceBuilder
{
    protected $invoice;

    public readonly DocumentService $documentService;

    public readonly DatabaseService $databaseService;

    public function __construct()
    {
        // LIBRERÍA EXTERNA PARA OBTENER INFORMACIÓN DE LOS DOCUMENTOS (SE USA EN VARIAS PARTES DE LA LIBRERÍA)
        $this->documentService = new DocumentService;

        // OBTENER INFORMACIÓN DE LA LIBRERÍA DESDE EL CLIENTE
        $this->databaseService = new DatabaseService;
    }

    /*
     * Declaro
     */
    public function setInvoice($invoice): void
    {
        $this->invoice = $invoice;

        $this->documentService->setInvoice(invoice: $invoice);
        $this->databaseService->setInvoice(invoice: $invoice);
    }
}
