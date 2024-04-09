<?php

namespace JiagBrody\LaravelFacturaMx\Http\Controllers;

use JiagBrody\LaravelFacturaMx\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();

        // return view('laravel-factura-mx::invoices.index', ['invoices' => $invoices]);
        return view('factura-mx::invoices.index', ['invoices' => $invoices]);
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
            'documents'
        ])->whereId($invoiceId)->firstOrFail();

        return view('factura-mx::invoices.show', ['invoice' => $invoice]);
    }

    public function store()
    {
        // Let's assume we need to be authenticated
        // to create a new post
        if (!auth()->check()) {
            abort(403, 'Only authenticated users can create new posts.');
        }

        dd(auth()->user());

        // request()->validate([
        //     'title' => 'required',
        //     'body'  => 'required',
        // ]);

        // $author = auth()->user();

        // $post = $author->posts()->create([
        //     'title'     => request('title'),
        //     'body'      => request('body'),
        // ]);

        // return redirect(route('posts.show', $post));
    }
}
