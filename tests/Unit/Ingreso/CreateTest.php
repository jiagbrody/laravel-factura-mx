<?php declare(strict_types=1);

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

it('create object', function () {

    $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx();

    $company = InvoiceCompany::factory()->create(['name' => 'ok']);

    $object = $facturaMx->ingreso()->create()->custom($company);

    expect($object)->toBeObject();
});
