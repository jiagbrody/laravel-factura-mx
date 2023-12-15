<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso;

use JiagBrody\LaravelFacturaMx\Models\Invoice;

interface SaveIngresoInterface
{
    public function toInvoice(string $relationshipModel, int $relationshipId, int $companyHelperId);

    public function toInvoiceDetails(Invoice $invoice);

    public function toInvoiceBalances(Invoice $invoice);

    public function toInvoiceTaxes(Invoice $invoice);
}
