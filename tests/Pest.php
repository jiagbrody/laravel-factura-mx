<?php

use JiagBrody\LaravelFacturaMx\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/*
 * Los tests que sellan o timbran un CFDI necesitan el CSD de pruebas oficial
 * del SAT (EKU9003173C9 "ESCUELA KEMPER URGATE") dentro de "sat_files_path".
 * Las rutas deben coincidir con las de InvoiceCompanyFactory.
 * Descarga: http://omawww.sat.gob.mx/tramitesyservicios/Paginas/certificado_sello_digital.htm
 */
function satTestCsdFilesExist(): bool
{
    $base = config('jiagbrody-laravel-factura-mx.sat_files_path');

    return is_file($base.'/csd_eku9003173c9_20190617131829/CSD_Sucursal_1_EKU9003173C9_20230517_223850.cer')
        && is_file($base.'/csd_eku9003173c9_20190617131829/CSD_Sucursal_1_EKU9003173C9_20230517_223850.key');
}
