<?php

declare(strict_types=1);

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

it('create object', function () {

    // $user = User::factory()->create();

    // dd(auth()->id());

    $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx;

    $company = InvoiceCompany::factory()->create();

    $object = $facturaMx->ingreso()->create()->custom($company);

    $atributos = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
    $atributos->setFolio('2');
    $atributos->setSerie('PATIENT');
    $atributos->setFormaPago('01');
    $atributos->setMoneda('MXN');
    $atributos->setTipoDeComprobante('I');
    $atributos->setExportacion('01');
    $atributos->setMetodoPago('PUE');
    $atributos->setLugarExpedicion('63732');
    $atributos->setSubTotal(6084.14);
    $atributos->setTotal(7057.60);
    $atributos->setDescuento(0.00);
    $object->addAtributos($atributos);

    $receptor = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ReceptorAtributos;
    $receptor->setNombre('Israel Alvarez');
    $receptor->setRfc('XAXX010101000');
    $object->addReceptor($receptor);

    $conceptos = collect();
    for ($i = 1; $i <= 3; $i++) {
        $concepto = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
        $concepto->setClaveProdServ('01010101');
        $concepto->setNoIdentificacion('31');
        $concepto->setCantidad('1');
        $concepto->setClaveUnidad('H87');
        $concepto->setDescripcion('BAUMANOMETRO DIGITAL');
        $concepto->setValorUnitario(1360);
        $concepto->setImporte(1360);
        $concepto->setDescuento(0);
        $concepto->setObjetoImp('02');

        for ($j = 1; $j <= 2; $j++) {
            $traslado = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
            $traslado->setBase(1360);
            $traslado->setImpuesto('002');
            $traslado->setTipoFactor('0.160000');
            $traslado->setTasaOCuota('TasaOCuota');
            $traslado->setImporte(217.6);
            $concepto->addImpuestoTraslado($traslado);

            $retencion = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoRetenidoAtributos();
            $retencion->setBase(1360);
            $retencion->setImpuesto('002');
            $retencion->setTipoFactor('0.160000');
            $retencion->setTasaOCuota('TasaOCuota');
            $retencion->setImporte(217.6);
            $concepto->addImpuestoRetenido($retencion);
        }
        $conceptos->push($concepto);
    }
    $object->addConceptos($conceptos);

    $retencionesLocales = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\RetencionesLocalesAtributos;
    $retencionesLocales->setImpLocRetenido('1');
    $retencionesLocales->setTasadeRetencion('2');
    $retencionesLocales->setImporte('3');
    $retencionesLocales = collect([$retencionesLocales]);
    $object->addComplementoImpuestosLocales($retencionesLocales);

    dd($object->build()->saveInvoice('quickSale', 999));

    expect($object)->toBeObject();
});
