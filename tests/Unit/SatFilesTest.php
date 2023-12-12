<?php declare(strict_types=1);

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;

describe('existing SAT files', function () {
    it('certificate file (.cer)', function () {
        $invoiceCompany = InvoiceCompany::factory()->create();
        $companyHelper  = new InvoiceCompanyHelper($invoiceCompany);

        expect($companyHelper->certificatePath)->toBeFile();
    });

    it('key file (.key)', function () {
        $invoiceCompany = InvoiceCompany::factory()->create();
        $companyHelper  = new InvoiceCompanyHelper($invoiceCompany);

        expect($companyHelper->keyPath)->toBeFile();
    });
});
