<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
