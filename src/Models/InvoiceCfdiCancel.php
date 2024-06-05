<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCfdiCancel extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_cfdi_cancel_type_id'];


    public function invoiceCfdi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InvoiceCfdi::class);
    }
}
