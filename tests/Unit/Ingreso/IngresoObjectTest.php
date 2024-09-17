<?php

describe('object creation', function () {
    $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx;
    it('ingreso', function () use ($facturaMx) {
        $object = $facturaMx->create();

        expect($object)->toBeObject();
    });

    it('create', function () use ($facturaMx) {
        $object = $facturaMx->create()->ingreso();

        expect($object)->toBeObject();
    });
});
