<?php

use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoicePaymentTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTaxTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;

\JiagBrody\LaravelFacturaMx\Models\InvoiceCompany::insert([
    [
        'name' => 'Emisor 1',
        'rfc' => 'EKU9003173C9',
        'nombre' => 'ESCUELA KEMPER URGATE',
        'domicilio_fiscal_receptor' => '21855',
        'regimen_fiscal' => '601',
        'certificate_path' => '/csd_eku9003173c9_20190617131829/CSD_Sucursal_1_EKU9003173C9_20230517_223850.cer',
        'key_path' => '/csd_eku9003173c9_20190617131829/CSD_Sucursal_1_EKU9003173C9_20230517_223850.key',
        'pass_phrase' => '12345678a',
        'serial_number' => '30001000000400002434',
    ],
    [
        'name' => 'Emisor 2',
        'rfc' => 'IIA040805DZ4',
        'nombre' => 'INDISTRIA ILUMINADORA DE ALMACENES',
        'domicilio_fiscal_receptor' => '63900',
        'regimen_fiscal' => '601',
        'certificate_path' => '/csd_iia040805dz4_20190617133200/CSD_Sucursal_1_IIA040805DZ4_20230518_062510.cer',
        'key_path' => '/csd_iia040805dz4_20190617133200/CSD_Sucursal_1_IIA040805DZ4_20230518_062510.key',
        'pass_phrase' => '12345678a',
        'serial_number' => '30001000000400002447',
    ],
]);

\JiagBrody\LaravelFacturaMx\Models\InvoiceType::insert([
    ['id' => InvoiceTypeEnum::INGRESO->value, 'name' => InvoiceTypeEnum::INGRESO->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceTypeEnum::EGRESO->value, 'name' => InvoiceTypeEnum::EGRESO->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceTypeEnum::TRASLADO->value, 'name' => InvoiceTypeEnum::TRASLADO->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceTypeEnum::NOMINA->value, 'name' => InvoiceTypeEnum::NOMINA->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceTypeEnum::PAGO->value, 'name' => InvoiceTypeEnum::PAGO->getName(), 'created_at' => now(), 'updated_at' => now()],
]);

\JiagBrody\LaravelFacturaMx\Models\InvoiceStatus::insert([
    ['id' => InvoiceStatusEnum::DRAFT->value, 'name' => InvoiceStatusEnum::DRAFT->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceStatusEnum::VIGENT->value, 'name' => InvoiceStatusEnum::VIGENT->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceStatusEnum::CANCELED->value, 'name' => InvoiceStatusEnum::CANCELED->getName(), 'created_at' => now(), 'updated_at' => now()],
]);

\JiagBrody\LaravelFacturaMx\Models\InvoicePaymentType::insert([
    ['id' => InvoicePaymentTypeEnum::PAGO_EN_UNA_EXHIBICION->value, 'name' => InvoicePaymentTypeEnum::PAGO_EN_UNA_EXHIBICION->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoicePaymentTypeEnum::PAGO_A_LINEA_DE_CREDITO->value, 'name' => InvoicePaymentTypeEnum::PAGO_A_LINEA_DE_CREDITO->getName(), 'created_at' => now(), 'updated_at' => now()],
]);

\JiagBrody\LaravelFacturaMx\Models\InvoiceTaxType::insert([
    ['id' => InvoiceTaxTypeEnum::TRASLADO->value, 'name' => InvoiceTaxTypeEnum::TRASLADO->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceTaxTypeEnum::RETENCION->value, 'name' => InvoiceTaxTypeEnum::RETENCION->getName(), 'created_at' => now(), 'updated_at' => now()],
]);

\JiagBrody\LaravelFacturaMx\Models\InvoiceDocumentType::insert([
    ['id' => InvoiceDocumentTypeEnum::XML_FILE->value, 'name' => InvoiceDocumentTypeEnum::XML_FILE->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceDocumentTypeEnum::PDF_FILE->value, 'name' => InvoiceDocumentTypeEnum::PDF_FILE->getName(), 'created_at' => now(), 'updated_at' => now()],
]);

\JiagBrody\LaravelFacturaMx\Models\InvoiceCfdiCancelType::insert([
    ['id' => InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED->value, 'name' => InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_UNRELATED->value, 'name' => InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_UNRELATED->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED->value, 'name' => InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED->getName(), 'created_at' => now(), 'updated_at' => now()],
    ['id' => InvoiceCfdiCancelTypeEnum::NEW_NORMATIVE_TO_GLOBAL->value, 'name' => InvoiceCfdiCancelTypeEnum::NEW_NORMATIVE_TO_GLOBAL->getName(), 'created_at' => now(), 'updated_at' => now()],
]);
