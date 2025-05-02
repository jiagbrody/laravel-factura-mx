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
            // update to canceled
            $invoice->invoice_status_id = InvoiceStatusEnum::CANCELED->value;
            $invoice->save();

            // get latest receipt SAT
            $receipt = $invoice->invoiceCfdi->invoiceCfdiCancelReceipts->last();

            // create cancel relationship with receipt
            $invoice->invoiceCfdi->invoiceCfdiCancel()->create([
                'invoice_cfdi_cancel_receipt_id' => $receipt->id,
            ]);
        });
    }
}
