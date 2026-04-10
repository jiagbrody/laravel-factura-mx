<?php

use JiagBrody\LaravelFacturaMx\LaravelFacturaMx;

describe('object creation', function () {
    $facturaMx = new LaravelFacturaMx;
    it('ingreso', function () use ($facturaMx) {
        $object = $facturaMx->create();

        expect($object)->toBeObject();
    });

    it('create', function () use ($facturaMx) {
        $object = $facturaMx->create()->ingreso();

        expect($object)->toBeObject();
    });
});
