<?php

// config for JiagBrody/LaravelFacturaMx
return [

    /*
     |--------------------------------------------------------------------------
     | Nombre de la columna a la que estará ligada las facturas para su modelo de negocio.
     |--------------------------------------------------------------------------
     |
     | Teniendo en cuenta que este es un paquete de facturación y como tal
     | debe de ir relacionado con su modelo de negocio.
     |
     | Previamente, usted debería de tener implementado su sistema de estados de cuenta:
     | cargos (lista de productos), pagos (pago total o abonos).
     |
     | IMPORTANTE: Este valor solo puede modificarse antes de correr las migraciones.
     |
     */

    'foreign_id_related_to_invoices' => 'statement_id',
    'foreign_id_related_to_concepts' => 'statement_detail_id',

    /*
     |--------------------------------------------------------------------------
     | Ruta de la carpeta para los archivos del SAT
     |--------------------------------------------------------------------------
     |
     | Especifica la carpeta donde estarán los archivos de los "Emisores"
     | estos archivos son los que proporciona el Servicio de Administración
     | Tributaria (SAT) para poder timbrar las facturas por medio de algún PAC
     | (servicio de terceros para timbrar facturas).
     |
     | Ejemplo:
     | storage_path('app/protected/sat-certificates')
     |
     */

    'sat_files_path' => __DIR__.'/../storage/app/protected/sat-certificates',

    /*
     |--------------------------------------------------------------------------
     | Name of the tables to migrate for the library
     |--------------------------------------------------------------------------
     |
     | Specifies the naming convention for all tables starting
     | with the word "invoice" and all tables starting with the word "cfdi".
     |
     */

    // 'migrations' => [
    //     'invoice_table_name' => 'invoice',
    //     'cfdi_table_name'    => 'cfdi',
    // ],
];
