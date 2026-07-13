<?php

declare(strict_types=1);

use JiagBrody\LaravelFacturaMx\LaravelFacturaMx;

describe('object creation', function () {
    it('ingreso', function () {
        $object = (new LaravelFacturaMx)->create();

        expect($object)->toBeObject();
    });

    it('create', function () {
        $object = (new LaravelFacturaMx)->create()->ingreso();

        expect($object)->toBeObject();
    });
});
