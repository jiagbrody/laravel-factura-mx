<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

interface DraftSatInterface
{
    public function custom(InvoiceCompany $company);

    public function fromComprobante(array $comprobante, Collection $products);
}
