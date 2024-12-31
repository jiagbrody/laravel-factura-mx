<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Read;

readonly class ReadInvoiceBuilder
{
    public ReadSpecificByInvoiceBuild $specifyInvoiceReading;

    public ReadAllInvoicesBuild $specifyAllInvoicesReading;

    public function __construct()
    {
        $this->specifyInvoiceReading = new ReadSpecificByInvoiceBuild();

        $this->specifyAllInvoicesReading = new ReadAllInvoicesBuild();
    }
}
