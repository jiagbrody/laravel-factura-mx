<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Actions;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;

class UpdateRecordsIfTheInvoiceHasBeenSentByThePacToCancelAction
{
    public function make(
        InvoiceCfdi $invoiceCfdi,
        InvoiceCfdiCancelTypeEnum $cancelTypeEnum,
        ?InvoiceCfdi $replacementInvoiceCfdi,
        string $xmlFile,
        ?string $fileName = null
    ): void {
        DB::transaction(function () use ($replacementInvoiceCfdi, $invoiceCfdi, $cancelTypeEnum, $xmlFile, $fileName) {
            // NOTA: Aunque procede el acuse no significa que ya esté cancelada la factura. Hay que
            // estar revisando el STATUS de la factura para que cambie a "cancelado".
            $cancel = $invoiceCfdi->InvoiceCfdiCancelReceipts()->create([
                'invoice_cfdi_cancel_type_id' => $cancelTypeEnum->value,
                'replacement_invoice_cfdi_id' => ($replacementInvoiceCfdi !== null) ? $replacementInvoiceCfdi->id : null,
                'receipt_date' => now(),
            ]);

            // Guardo del Acuse de la cancelación
            (new DocumentRepository)->create(
                relationshipModel: $cancel->getMorphClass(),
                relationshipId: $cancel->id,
                documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
                fileName: ($fileName === null) ? '' : $fileName,
                filePath: 'files/acuses',
                mimeType: InvoiceDocumentTypeEnum::XML_FILE->getMimeType(),
                extension: InvoiceDocumentTypeEnum::XML_FILE->getExtension(),
                storage: 'public',
                fileContent: $xmlFile,
            );
        });
    }
}
