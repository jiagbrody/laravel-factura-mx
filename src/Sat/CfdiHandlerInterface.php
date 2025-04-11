<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

interface CfdiHandlerInterface
{
    public function custom(InvoiceCompany $company);
}
