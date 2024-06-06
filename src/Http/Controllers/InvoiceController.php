<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with([
            'invoiceType',
            'invoiceCompany',
            'invoiceStatus',
            'invoiceCfdi',
        ])->get();

        return Inertia::render('laravel-factura-mx/Invoices/Index', [
            'invoices' => $invoices,
            'cat_invoice_cfdi_cancel_types' => InvoiceCfdiCancelTypeEnum::getCatalog(),
        ]);
    }

    public function show($invoiceId)
    {
        $invoice = Invoice::with([
            'invoiceType',
            'invoiceCompany',
            'invoiceStatus',
            'invoiceBalance',
            'invoiceCfdi',
            'invoiceDetail',
            'invoiceTaxes',
            'invoiceTax',
            'documents',
        ])->whereId($invoiceId)->firstOrFail();

        return Inertia::render('laravel-factura-mx/Invoices/Show', ['invoice' => $invoice]);
    }

    public function getCancelData()
    {
        return response()->json([
            'cat_invoice_cfdi_cancel_types' => InvoiceCfdiCancelTypeEnum::getCatalog(),
        ]);
    }

    public function SetCancel(Request $request, Invoice $invoice)
    {
        $request->validate([
            'invoice_id' => ['required', 'integer'],
            'invoice_cfdi_cancel_type_id' => ['required', 'integer'],
            'uuid' => ['required_if:cat_invoice_cfdi_cancel_type,'.InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED->value, 'sometimes', 'nullable'],
        ]);

        $invoice->load('invoiceCfdi');
        $response = null;

        if ($invoice->invoiceCfdi) {
            $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx;
            $cancelType = InvoiceCfdiCancelTypeEnum::from($request->input('invoice_cfdi_cancel_type_id'));

            $cancelBuilder = $facturaMx->cancel()
                ->setInvoice($invoice)
                ->setPacProvider()
                ->setCancelTypeEnum($cancelType);

            if ($cancelType === InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED) {
                $cancelBuilder->setReplacementUUID($request->input('uuid'));
            }

            $response = $cancelBuilder->build();
            dd($response);

            if ($response->checkProcess === false) {
                dd($response);
            }
        }

        return back();
    }

    // public function destroy(Invoice $invoice)
    // {
    //
    // }

    public function getStatus(Invoice $invoice)
    {
        $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx;

        $response = $facturaMx->status()
            ->setInvoice($invoice)
            ->setPacProvider()
            ->build();

        if ($response->checkProcess === false) {
            dd($response);
        }

        return response()->json(['pac_response' => $response]);
    }
}
