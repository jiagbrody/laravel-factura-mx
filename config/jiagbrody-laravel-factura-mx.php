<?php

// config for JiagBrody/LaravelFacturaMx
return [

    /*
     |-----------------------------------------------------------------------------------------------
     | Nombre de las tablas y columnas a la que estará ligada las facturas para su modelo de negocio.
     |-----------------------------------------------------------------------------------------------
     |
     | Teniendo en cuenta que este es un paquete de facturación y como tal
     | debe de ir relacionado con su modelo de negocio.
     |
     | Previamente, usted debería de tener implementado su sistema de estados de cuenta:
     | cargos (lista de productos), pagos (pago total o abonos).
     |
     | IMPORTANTE: Estos valores solo pueden modificarse antes de correr las migraciones.
     |
     */
    'table_names' => [
        'invoice_companies' => 'jiagbrody_lfmx_invoice_companies',
        'invoice_types' => 'jiagbrody_lfmx_invoice_types',
        'invoice_statuses' => 'jiagbrody_lfmx_invoice_statuses',
        'invoices' => 'jiagbrody_lfmx_invoices',
        'invoice_details' => 'jiagbrody_lfmx_invoice_details',
        'invoice_payment_types' => 'jiagbrody_lfmx_invoice_payment_types',
        'invoice_balances' => 'jiagbrody_lfmx_invoice_balances',
        'invoice_tax_types' => 'jiagbrody_lfmx_invoice_tax_types',
        'invoice_taxes' => 'jiagbrody_lfmx_invoice_taxes',
        'invoice_tax_details' => 'jiagbrody_lfmx_invoice_tax_details',
        'invoice_related_concept_pivot' => 'jiagbrody_lfmx_invoice_related_concept_pivot',
        'invoice_document_types' => 'jiagbrody_lfmx_invoice_document_types',
        'invoice_documents' => 'jiagbrody_lfmx_invoice_documents',
        'invoice_cfdi_cancel_types' => 'jiagbrody_lfmx_invoice_cfdi_cancel_types',
        'invoice_cfdis' => 'jiagbrody_lfmx_invoice_cfdis',
        'invoice_cfdi_cancels' => 'jiagbrody_lfmx_invoice_cfdi_cancels',
    ],

    'column_names' => [
        /*
         * Campo relacionado con el modelo de negocio del los conceptos del estado de cuenta
         */
        'foreign_id_related_to_concepts' => 'statement_detail_id',
    ],

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
     | NOTA: Esta ubicación tiene que estar protegida o no tener acceso
     | público por internet.
     |
     */

    'sat_files_path' => __DIR__.'/../storage/app/protected/sat-certificates',

    /*
     |--------------------------------------------------------------------------
     | Almacenamiento local de recursos del SAT: archivos XSLT
     |--------------------------------------------------------------------------
     |
     | El SAT publica diferentes recursos para diferentes tareas,
     | los recursos más usuales son:
     |
     | Archivos XSD: Son archivos de esquemas XML y sirven para comprobar que
     | un archivo es correcto con respecto a ciertas reglas.
     | Archivos XSLT: Son archivos de transformaciones XML y sirven para
     | transformar el contenido de un archivo XML en otro contenido.
     | El SAT los utiliza para generar cadenas de origen.
     | Archivos CER: Son archivos de certificado comúnmente utilizados para
     | verificar que una firma es válida con respecto a un emisor. La firma es
     | lo que el sat llama sello y el emisor se distingue por un certificado.
     |
     | Estos recursos están disponibles en internet, pero son grandes y tienen
     | cambios esporádicos. Por ejemplo, el archivo de catálogos del SAT
     | mide 6.3 MB. Por ello es conveniente tener una copia local de los recursos.
     |
     */

    'sat_local_resource_path' => __DIR__.'/../storage/app/protected/sat-resources',

    /*
     |--------------------------------------------------------------------------
     | Ruta de la carpeta para los archivos de las facturas
     |--------------------------------------------------------------------------
     |
     | Especifica la carpeta donde estarán los archivos de las facturas
     | archivos XML (CFDI) y PDF (formato legible del CFDI).
     |
     | "filesystem_disk" es el sistema nativo de Laravel el cual se puede
     | configurar en "config/filesystems.php".
     | Nativos default Laravel: "local", "ftp", "sftp", "s3", "public"
     |
     | "invoices_files_path" es la carpeta dentro del storage donde se
     | almacenaran los archivos y facturas de la librería.
     |
     */
    'filesystem_disk' => 'public',
    'invoices_files_path' => 'files/invoices',

    /*
     |--------------------------------------------------------------------------
     | Ruta de la carpeta para los archivos de las facturas
     |--------------------------------------------------------------------------
     |
     | Especifica la carpeta donde estarán los archivos de las facturas
     | archivos XML (CFDI) y PDF (formato legible del CFDI)
     |
     */

    'default_timezone' => 'America/Mexico_City',

    /*
     |--------------------------------------------------------------------------
     | Proveedores de PACS
     |--------------------------------------------------------------------------
     |
     | Especifica el proveedor para timbrar factura y su entorno.
     |
     | "pac_environment_production": Declara si el entorno es para ambiente de
     | producción, si es falso entonces será de entorno de pruebas.
     | 2 valores posibles: true, false.
     |
     | "pac_chosen": Elección de la lista de proveedores, actualmente solo está
     | disponible "finkok".
     |
     | "pac_providers": De momento solo está desarrollado con el proveedor Finkok,
     | ya que la mayoría de personas de la comunidad que conozco es el que usan
     | y el que menos falla. Es muy común que cada PAC haga sus propias reglas y
     | validaciones para timbrar. He visto demasiados PACS que el problema
     | de timbrar está de su lado por no estar actualizado con las últimas
     | modificaciones del SAT y esto es un error gravísimo.
     |
     | https://www.finkok.com
     |
     | Aquí es sumamente importante haber generado su cuenta con el proveedor.
     |
     */

    'pac_environment_production' => false,
    'pac_chosen' => 'finkok',
    'pac_providers' => [
        'finkok' => [
            'user' => 'israel.alvarez@hospitalcmq.com',
            'password' => '0o$69Uh06o*r',
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Prefijo para los archivos timbrados
     |--------------------------------------------------------------------------
     |
     | Coloca un prefijo al principio del nombre del archivo timbrado.
     |
     */

    'prefix_for_stamped_files' => 'cfdi-',

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

    /*
     |--------------------------------------------------------------------------
     | Database name for SAT catalogs
     |--------------------------------------------------------------------------
     |
     | These are the catalogs regulated by the SAT for the creation of CFDI.
     | As is usually a very heavy and complex database. It is recommended to
     | handle it with this type of engine (sqlite) for greater speed and reading.
     |
     */

    'sqlite-sat-catalogs' => [
        'driver' => 'sqlite',
        'url' => '',
        'database' => '/app/protected/sat-database-catalogs/catalogs_sat_40.db',
        'prefix' => '',
        'foreign_key_constraints' => true,
    ],

];
