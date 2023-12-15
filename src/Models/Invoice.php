<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public function invoiceBalance(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InvoiceBalance::class);
    }

    public function invoiceDetails(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InvoiceDetail::class);
    }

    public function invoiceTaxes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceTax::class);
    }

    public function invoiceTax(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InvoiceTax::class);
    }

    /**
     * Ver de qué manera relaciono los "invoices" con los "statement_details" de la relación de productos.
     */
    // public function statementDetails()
    // {
    //     return $this->belongsToMany('');
    // }
}
