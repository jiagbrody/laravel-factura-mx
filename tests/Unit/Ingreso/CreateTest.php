<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

it('create object', function () {

    // $user = User::factory()->create();

    // dd(auth()->id());

    $facturaMx = new \JiagBrody\LaravelFacturaMx\LaravelFacturaMx;

    $company = InvoiceCompany::factory()->create();

    $object = $facturaMx->create()->ingreso()->custom($company);

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

    $products = getProducts();
    $products->each(function (Collection $product) {
        $concepto = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
        $concepto->setClaveProdServ('01010101');
        $concepto->setNoIdentificacion((string) $product->get('statement_detail_id'));
        $concepto->setCantidad((string) $product->get('quantity'));
        $concepto->setClaveUnidad('H87');
        $concepto->setDescripcion((string) $product->get('name'));
        $concepto->setValorUnitario((float) $product->get('price_unit'));
        $concepto->setImporte((float) $product->get('total'));
        $concepto->setDescuento((float) $product->get('discount'));
        $concepto->setObjetoImp('02');

        for ($j = 1; $j <= 2; $j++) {
            $traslado = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
            $traslado->setBase(1360);
            $traslado->setImpuesto('002');
            $traslado->setTipoFactor('Tasa');
            $traslado->setTasaOCuota('0.160000');
            $traslado->setImporte(217.6);
            $concepto->addImpuestoTraslado($traslado);

            $retencion = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoRetenidoAtributos();
            $retencion->setBase(1360);
            $retencion->setImpuesto('002');
            $retencion->setTipoFactor('Tasa');
            $retencion->setTasaOCuota('0.160000');
            $retencion->setImporte(217.6);
            $concepto->addImpuestoRetenido($retencion);
        }
        $product->put('conceptSat', $concepto);
    });

    $object->addConceptos($products);

    $localTaxes = getLocalTaxes();
    $localTaxes->each(function (Collection $tax) {
        $retencionesLocales = new \JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\RetencionesLocalesAtributos;
        $retencionesLocales->setImpLocRetenido('Impuesto Cedular');
        $retencionesLocales->setTasadeRetencion('3.00');
        $retencionesLocales->setImporte((string) $tax->get('amount'));
        $tax->put('localTaxSat', $retencionesLocales);
    });

    $object->addComplementoImpuestosLocales($localTaxes);

    $object->build()->saveInvoice('quickSale', 999);

    expect($object)->toBeObject();
});

function getProducts(): Collection
{
    $collection = collect();
    $collection->push(collect([
        'id' => 5679,
        'product_id' => 5679,
        'statement_detail_id' => 7,
        'statement_id' => 1,
        'product_historical_id' => 5682,
        'product_price_historical_id' => 5682,
        'product_sat_key_historical_id' => 5679,
        'user_id' => 53,
        'quantity' => 1,
        'comments' => 'dolorum vitae magnam molestiae',
        'statement_company_agreement_id' => 1,
        'insurance_item_will_be_paid_patient' => true,
        'statement_detail_bundle_id' => null,
        'statement_requisition_id' => null,
        'created_at' => '2023-10-24T18:11:21.000000Z',
        'updated_at' => '2023-12-14T19:00:06.000000Z',
        'department_id' => 33,
        'department_name' => 'Coaseguro',
        'department_code' => 'CO33',
        'section_id' => 16,
        'product_type_id' => 6,
        'code' => 'COAHOS',
        'name' => 'Coaseguro Hospital',
        'alias' => 'Coaseguro Hospital',
        'inventory' => false,
        'invoice_company_id' => 1,
        'c_clave_prod_serv' => '84111506',
        'c_clave_unidad' => 'ACT',
        'c_tipo_impuesto' => 'Traslados',
        'c_impuesto' => '002',
        'c_tipo_factor' => 'Tasa',
        'c_tasa_o_cuota' => '0.160000',
        'c_objeto_impuesto' => '02',
        'enable_iva' => true,
        'enable_discount' => false,
        'expiration_ph' => null,
        'status_ph' => 3,
        'product_price_id' => 5679,
        'price_unit_default' => 8.62069,
        'price_unit_insurance' => 8.62069,
        'expiration_pph' => null,
        'status_pph' => 3,
        'custom_discount_percent_decimal' => null,
        'custom_discount_value' => null,
        'invoice_id' => null,
        'invoice_is_draft' => null,
        'invoice_tipo_de_comprobante' => null,
        'invoice_metodo_pago' => null,
        'cfdi_id' => null,
        'price_unit' => 8.62069,
        'created_at_format' => '10/24/2023',
        'created_at_human' => 'hace 1 mes',
        'gross_sub_total' => 8.62069,
        'gross_tax' => 1.3793104,
        'gross_total' => 10.0000004,
        'increase_applied' => false,
        'increase_unit' => 0.0,
        'increase' => 0.0,
        'discount_applied' => false,
        'discount_unit' => 0.0,
        'discount' => 0.0,
        'sub_total' => 8.62069,
        'tax' => 1.3793104,
        'total' => 10.0000004,
        'text_type' => '',
        'sku' => '33.5679.COAHOS',
        'sku_name' => '33.5679.COAHOS.Coaseguro Hospital',
    ]));
    $collection->push(collect([
        'id' => 1,
        'product_id' => 1,
        'statement_detail_id' => 1,
        'statement_id' => 1,
        'product_historical_id' => 1,
        'product_price_historical_id' => 1,
        'product_sat_key_historical_id' => 1,
        'user_id' => 116,
        'quantity' => 1,
        'comments' => 'et unde dolores sint',
        'statement_company_agreement_id' => 1,
        'insurance_item_will_be_paid_patient' => true,
        'statement_detail_bundle_id' => null,
        'statement_requisition_id' => null,
        'created_at' => '2023-10-24T18:11:21.000000Z',
        'updated_at' => '2023-10-24T18:11:21.000000Z',
        'department_id' => 1,
        'department_name' => 'Estudios de Rayos X',
        'department_code' => 'ES1',
        'section_id' => 66,
        'product_type_id' => 2,
        'code' => 'ES-1',
        'name' => 'RX ABDOMEN 1 POS.',
        'alias' => 'RX ABDOMEN 1 POS.',
        'inventory' => false,
        'invoice_company_id' => 1,
        'c_clave_prod_serv' => '85121808',
        'c_clave_unidad' => 'E48',
        'c_tipo_impuesto' => 'Traslados',
        'c_impuesto' => '002',
        'c_tipo_factor' => 'Tasa',
        'c_tasa_o_cuota' => '0.160000',
        'c_objeto_impuesto' => '02',
        'enable_iva' => true,
        'enable_discount' => true,
        'expiration_ph' => null,
        'status_ph' => 1,
        'product_price_id' => 1,
        'price_unit_default' => 587.069,
        'price_unit_insurance' => 550.914,
        'expiration_pph' => null,
        'status_pph' => 1,
        'custom_discount_percent_decimal' => null,
        'custom_discount_value' => null,
        'invoice_id' => null,
        'invoice_is_draft' => null,
        'invoice_tipo_de_comprobante' => null,
        'invoice_metodo_pago' => null,
        'cfdi_id' => null,
        'price_unit' => 587.069,
        'created_at_format' => '10/24/2023',
        'created_at_human' => 'hace 1 mes',
        'gross_sub_total' => 587.069,
        'gross_tax' => 93.93104,
        'gross_total' => 681.00004,
        'increase_applied' => false,
        'increase_unit' => 0.0,
        'increase' => 0.0,
        'discount_applied' => true,
        'discount_unit' => 58.7069,
        'discount' => 58.7069,
        'sub_total' => 528.3621,
        'tax' => 84.537936,
        'total' => 612.900036,
        'text_type' => 'Servicio',
        'sku' => '1.1.ES-1',
        'sku_name' => '1.1.ES-1.RX ABDOMEN 1 POS.',
    ]));
    $collection->push(collect([
        'id' => 5680,
        'product_id' => 5680,
        'statement_detail_id' => 8,
        'statement_id' => 1,
        'product_historical_id' => 5683,
        'product_price_historical_id' => 5683,
        'product_sat_key_historical_id' => 5680,
        'user_id' => 51,
        'quantity' => 1,
        'comments' => 'nihil exercitationem amet quis',
        'statement_company_agreement_id' => 1,
        'insurance_item_will_be_paid_patient' => true,
        'statement_detail_bundle_id' => null,
        'statement_requisition_id' => null,
        'created_at' => '2023-10-24T18:11:21.000000Z',
        'updated_at' => '2023-12-14T19:00:06.000000Z',
        'department_id' => 33,
        'department_name' => 'Coaseguro',
        'department_code' => 'CO33',
        'section_id' => 16,
        'product_type_id' => 7,
        'code' => 'COAHM',
        'name' => 'Coaseguro Honorarios Médicos',
        'alias' => 'Coaseguro Honorarios Médicos',
        'inventory' => false,
        'invoice_company_id' => 1,
        'c_clave_prod_serv' => '84111506',
        'c_clave_unidad' => 'ACT',
        'c_tipo_impuesto' => 'Traslados',
        'c_impuesto' => '002',
        'c_tipo_factor' => 'Tasa',
        'c_tasa_o_cuota' => '0.160000',
        'c_objeto_impuesto' => '02',
        'enable_iva' => true,
        'enable_discount' => false,
        'expiration_ph' => null,
        'status_ph' => 3,
        'product_price_id' => 5680,
        'price_unit_default' => 172.413793,
        'price_unit_insurance' => 172.413793,
        'expiration_pph' => null,
        'status_pph' => 3,
        'custom_discount_percent_decimal' => null,
        'custom_discount_value' => null,
        'invoice_id' => null,
        'invoice_is_draft' => null,
        'invoice_tipo_de_comprobante' => null,
        'invoice_metodo_pago' => null,
        'cfdi_id' => null,
        'price_unit' => 172.413793,
        'created_at_format' => '10/24/2023',
        'created_at_human' => 'hace 1 mes',
        'gross_sub_total' => 172.413793,
        'gross_tax' => 27.58620688,
        'gross_total' => 199.99999988,
        'increase_applied' => false,
        'increase_unit' => 0.0,
        'increase' => 0.0,
        'discount_applied' => false,
        'discount_unit' => 0.0,
        'discount' => 0.0,
        'sub_total' => 172.413793,
        'tax' => 27.58620688,
        'total' => 199.99999988,
        'text_type' => '',
        'sku' => '33.5680.COAHM',
        'sku_name' => '33.5680.COAHM.Coaseguro Honorarios Médicos',
    ]));
    $collection->push(collect([
        'id' => 5678,
        'product_id' => 5678,
        'statement_detail_id' => 6,
        'statement_id' => 1,
        'product_historical_id' => 5681,
        'product_price_historical_id' => 5681,
        'product_sat_key_historical_id' => 5678,
        'user_id' => 131,
        'quantity' => 1,
        'comments' => 'saepe cumque expedita tempora',
        'statement_company_agreement_id' => 1,
        'insurance_item_will_be_paid_patient' => true,
        'statement_detail_bundle_id' => null,
        'statement_requisition_id' => null,
        'created_at' => '2023-10-24T18:11:21.000000Z',
        'updated_at' => '2023-12-14T18:59:36.000000Z',
        'department_id' => 34,
        'department_name' => 'Deducibles',
        'department_code' => 'DE34',
        'section_id' => 16,
        'product_type_id' => 5,
        'code' => 'DEDUC',
        'name' => 'Deducibles',
        'alias' => 'Deducibles',
        'inventory' => false,
        'invoice_company_id' => 1,
        'c_clave_prod_serv' => '84111506',
        'c_clave_unidad' => 'ACT',
        'c_tipo_impuesto' => 'Traslados',
        'c_impuesto' => '002',
        'c_tipo_factor' => 'Tasa',
        'c_tasa_o_cuota' => '0.160000',
        'c_objeto_impuesto' => '02',
        'enable_iva' => true,
        'enable_discount' => false,
        'expiration_ph' => null,
        'status_ph' => 3,
        'product_price_id' => 5678,
        'price_unit_default' => 1810.344828,
        'price_unit_insurance' => 1810.344828,
        'expiration_pph' => null,
        'status_pph' => 3,
        'custom_discount_percent_decimal' => null,
        'custom_discount_value' => null,
        'invoice_id' => null,
        'invoice_is_draft' => null,
        'invoice_tipo_de_comprobante' => null,
        'invoice_metodo_pago' => null,
        'cfdi_id' => null,
        'price_unit' => 1810.344828,
        'created_at_format' => '10/24/2023',
        'created_at_human' => 'hace 1 mes',
        'gross_sub_total' => 1810.344828,
        'gross_tax' => 289.65517248,
        'gross_total' => 2100.00000048,
        'increase_applied' => false,
        'increase_unit' => 0.0,
        'increase' => 0.0,
        'discount_applied' => false,
        'discount_unit' => 0.0,
        'discount' => 0.0,
        'sub_total' => 1810.344828,
        'tax' => 289.65517248,
        'total' => 2100.00000048,
        'text_type' => '',
        'sku' => '34.5678.DEDUC',
        'sku_name' => '34.5678.DEDUC.Deducibles',
    ]));

    return $collection;
}

function getLocalTaxes(): Collection
{
    $collection = collect();
    $collection->push(collect([
        'amount' => 8,
    ]));
    $collection->push(collect([
        'amount' => 2,
    ]));

    return $collection;
}
