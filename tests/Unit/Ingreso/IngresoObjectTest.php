<?php

describe('object creation', function () {
    $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx();
    it('ingreso', function () use ($facturaMx) {
        $object = $facturaMx->ingreso();

        expect($object)->toBeObject();
    });

    it('create', function () use ($facturaMx) {
        $object = $facturaMx->ingreso()->create();

        expect($object)->toBeObject();
    });
});
