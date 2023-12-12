<?php declare(strict_types=1);

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

it('create object', function () {

    // $user = User::factory()->create();

    // dd(auth()->id());

    $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx;

    $company = InvoiceCompany::factory()->create();

    $object = $facturaMx->ingreso()->create()->custom($company);

    $atributos = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
    $atributos->setFolio('33829');
    $atributos->setSerie('PACIENTE');
    $object->addAtributos($atributos);

    $receptor = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ReceptorAtributos;
    $receptor->setNombre('Israel Alvarez');
    $receptor->setRfc('XAXX010101000');
    $object->addReceptor($receptor);

    $concepto = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
    $concepto->setCantidad('1');
    $concepto->setClaveProdServ('882921');
    $concepto->setDescripcion('producto de prueba');
    $concepto->setValorUnitario(98.998);
    $concepto->setDescuento(881.022);
    $traslado = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
    $traslado->setBase(220);
    $traslado->setImporte(221);
    $concepto->setImpuestoTrasladoAtributos($traslado);
    $conceptos = collect([$concepto]);
    $object->addConceptos($conceptos);

    $retencionesLocales = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\RetencionesLocalesAtributos;
    $retencionesLocales->setImpLocRetenido('1');
    $retencionesLocales->setTasadeRetencion('2');
    $retencionesLocales->setImporte('3');
    $retencionesLocales = collect([$retencionesLocales]);
    $object->addComplementoImpuestosLocales($retencionesLocales, 0);

    expect($object)->toBeObject();
});
