<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Actions;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;

class UpdateRecordsAfterCheckingInvoiceStatusAction
{
    public function __invoke(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            // ACTUALIZO EL NOMBRE DEL REGISTRO DE LA SOLICITUD PREVIA DEL PAC
            $invoice->invoice_status_id = InvoiceStatusEnum::CANCELED->value;
            $invoice->save();

            // QUITO LA RELACIÓN DE LOS PRODUCTOS DEL INVOICE PARA PODER GESTIONARLOS NUEVAMENTE (O HACER LA SUBSTITUCION DE LA FACTURA).
            // if ($invoice->invoice_cfdi_type_id === InvoiceTypeEnum::INGRESO->value) {
            //     $invoice->statementDetails()->detach();
            // }

            //TODO: CHECAR ESTA PARTE COMO SE DEBE QUEDAR BIEN LA CANCELACION DEL PAGO, SE SUPONE QUE SOLO ES UN CAMBIO DE REGISTRO, NO RECUERDO POR QUE EL "LOOP" PARA ACTUALIZAR.
            // if ($invoice->invoice_cfdi_type_id === InvoiceTypeEnum::PAGO->value) {
            //     $invoice->invoicePayments->each(function ($item) {
            //         $item->invoicePaymentDocuments()->update(['is_active' => false]);
            //     });
            // }

            // if ($invoice->invoiceCfdi->cfdiCancel && isset($sat->EstatusCancelacion)) {
            //     $invoice->invoiceCfdi->cfdiCancel->estatus_cancelacion = $sat->EstatusCancelacion;
            //     $invoice->invoiceCfdi->cfdiCancel->save();
            // }
        });
    }
}
