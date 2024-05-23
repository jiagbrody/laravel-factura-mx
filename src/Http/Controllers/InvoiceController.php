<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Http\Controllers;

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

    public function destroy(Invoice $invoice)
    {
        $invoice->load('invoiceCfdi');
        $response = null;

        if ($invoice->invoiceCfdi) {
            $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx;

            $response = $facturaMx->cancel()
                ->setInvoice($invoice)
                ->setPacProvider()
                ->setCancelTypeEnum(InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED)
                ->build();

            if ($response->checkProcess === false) {
                dd($response);
            }
        }

        return back();
    }

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
