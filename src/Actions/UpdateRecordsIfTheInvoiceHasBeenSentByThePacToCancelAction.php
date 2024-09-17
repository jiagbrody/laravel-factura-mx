<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Actions;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Sat\Document\DocumentHandler;

class UpdateRecordsIfTheInvoiceHasBeenSentByThePacToCancelAction
{
    public function __invoke(
        InvoiceCfdi $invoiceCfdi,
        InvoiceCfdiCancelTypeEnum $cancelTypeEnum,
        string $xmlFile,
        ?string $fileName = null
    ): void {
        DB::transaction(function () use ($invoiceCfdi, $cancelTypeEnum, $xmlFile, $fileName) {
            //NOTA: Aunque procede el acuse no significa que ya esté cancelada la factura. Hay que
            //estar revisando el STATUS de la factura para que cambie a "cancelado".
            $cancel = $invoiceCfdi->invoiceCfdiCancels()->create(['invoice_cfdi_cancel_type_id' => $cancelTypeEnum->value]);

            //Guardo del Acuse de la cancelación
            (new DocumentHandler)->create(
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
