# Laravel Factura MX — Facturación electrónica CFDI 4.0 para México

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jiagbrody/laravel-factura-mx.svg?style=flat-square)](https://packagist.org/packages/jiagbrody/laravel-factura-mx)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jiagbrody/laravel-factura-mx/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jiagbrody/laravel-factura-mx/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jiagbrody/laravel-factura-mx/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jiagbrody/laravel-factura-mx/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jiagbrody/laravel-factura-mx.svg?style=flat-square)](https://packagist.org/packages/jiagbrody/laravel-factura-mx)

Paquete de Laravel para la facturación electrónica en México (CFDI **4.0**): construcción del comprobante, sellado con el CSD del emisor, **timbrado** ante un PAC (Finkok), cancelación, consulta de estatus ante el SAT, recuperación de XML timbrados y generación del PDF legible con el QR oficial de verificación del SAT.

La documentación está en español porque es el idioma natural de los tecnicismos del SAT (Anexo 20).

📖 **[Mapa funcional de la librería](docs/mapa-funcional.md)** — todo lo que hace y con qué método: ciclo de vida del CFDI, las 8 áreas de funcionalidad, matriz de cobertura por tipo de comprobante, excepciones y configuración.

## Características

- Construcción fluida del CFDI 4.0 (ingreso, egreso/nota de crédito y complemento de pago) sobre [`eclipxe/cfdiutils`](https://github.com/eclipxe13/CfdiUtils).
- **Validación local** (XSD + reglas SAT) antes de enviar al PAC — los CFDI malformados no queman intentos de timbrado.
- Timbrado con Finkok resistente a sus estados no documentados: `Comprobante timbrado previamente` (307), timbrado asíncrono en cola, recuperación del resultado original vía `stamped` — todo normalizado en una sola respuesta (`getCheckProcess()`).
- Guard anti-doble-timbrado y guard de la ventana de 72 horas del SAT.
- Errores como **excepciones tipadas** (`FacturaMxException` y subclases) — seguras para colas y comandos, nunca `abort()`.
- Logs SOAP por operación en `storage/logs/cfdi_finkok_*.log` (con la contraseña del PAC redactada), también cuando la llamada falla.
- Persistencia post-timbrado opcional (estatus, UUID, XML, PDF, limpieza de borradores) o control total desde tu aplicación.
- Catálogos oficiales del SAT 4.0 servidos desde una base SQLite de solo lectura.

## Requisitos

- PHP ^8.2 con `ext-soap` y `ext-intl`
- Laravel (el paquete se desarrolla con Orchestra Testbench)
- Una cuenta con el PAC ([Finkok](https://www.finkok.com)) — demo o producción
- El CSD del emisor (`.cer`, `.key` y contraseña) emitido por el SAT

## Instalación

```bash
composer require jiagbrody/laravel-factura-mx
```

Publica el archivo de configuración:

```bash
php artisan vendor:publish --tag="jiagbrody-laravel-factura-mx-config"
```

Las migraciones se cargan directamente desde el paquete (no necesitas publicarlas):

```bash
php artisan migrate
```

> Los nombres de las tablas (prefijo `jiagbrody_lfmx_*`) son configurables en `table_names`, **solo antes de correr las migraciones**.

## Configuración esencial

```dotenv
FACTURA_MX_FINKOK_USER=tu_usuario_finkok
FACTURA_MX_FINKOK_PASSWORD=tu_password_finkok
```

| Clave | Qué es |
|---|---|
| `pac_environment_production` | `false` = demo de Finkok, `true` = producción. Las URLs oficiales de ambos entornos vienen incluidas. |
| `sat_files_path` | Carpeta (NO pública) con los CSD de los emisores. Cada emisor se registra en la tabla `invoice_companies` con las rutas de su `.cer`/`.key`. |
| `sat_local_resource_path` | Copia local de los XSD/XSLT del SAT. Se descargan automáticamente la primera vez que se validan/sellan comprobantes. |
| `sqlite-sat-catalogs` | Ruta de la base SQLite con los catálogos oficiales del SAT 4.0 (régimen fiscal, usos, formas de pago, monedas…). |
| `filesystem_disk` / `invoices_files_path` | Dónde se guardan XML/PDF. Default: disco privado `local` — los CFDI contienen datos personales. |
| `pre_validate_cfdi` | Valida el CFDI localmente al construirlo (default `true`). |
| `stamp_draft_max_age_hours` | Bloquea el envío de borradores con `Fecha` más vieja que N horas (default `71`; el SAT rechaza a las 72). |
| `persist_stamp_result` | Si `true` (default), `build()` persiste todo al timbrar. Ponlo en `false` si tu app tiene su propia orquestación de persistencia. |
| `generate_pdf_on_stamp` / `pdf_invoice_view` | Generación del PDF legible y vista Blade a usar (copia la default con otro nombre para personalizarla). |
| `pac_soap_timeout_seconds` | Timeout de cada llamada SOAP al PAC (default 30). |

## Uso

### Crear un CFDI de ingreso (borrador sellado)

```php
use JiagBrody\LaravelFacturaMx\Facades\LaravelFacturaMx;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\{ComprobanteAtributos, ReceptorAtributos, ConceptoAtributos, ImpuestoTrasladoAtributos};

$company = InvoiceCompany::find(1); // el emisor (con su CSD registrado)

$voucher = LaravelFacturaMx::create()->ingreso()->custom($company);

$atributos = new ComprobanteAtributos;   // Fecha default: ahora, en default_timezone
$atributos->setSerie('A');
$atributos->setFolio('1');
$atributos->setFormaPago('01');
$atributos->setMetodoPago('PUE');
$atributos->setMoneda('MXN');
$atributos->setTipoDeComprobante('I');
$atributos->setExportacion('01');
$atributos->setLugarExpedicion('63732');
$voucher->addAtributos($atributos);

$receptor = new ReceptorAtributos;
$receptor->setRfc('XAXX010101000');
$receptor->setNombre('PUBLICO EN GENERAL');
$voucher->addReceptor($receptor);

// ... addConceptos(), addRelacionados(), addComplementoImpuestosLocales() ...

$invoice = $voucher->build()->createNewInvoice(); // valida, sella y persiste el borrador
```

### Timbrar

```php
$response = LaravelFacturaMx::invoiceStamper($invoice)->build();

if ($response->getCheckProcess()) {
    $uuid = $response->getUuid();   // UUID fiscal
    $xml = $response->getXml();     // XML timbrado
} else {
    $motivo = $response->getIncidenciaMensaje(); // incidencia del PAC (también se guarda en invoice_incidents)
}
```

Con `persist_stamp_result = true` (default), el timbrado exitoso deja la factura vigente con su CFDI, XML, PDF y sin borradores — en la misma llamada.

### Cancelar, estatus y recuperación

```php
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;

// Cancelación ante el SAT (persiste el acuse cuando procede)
$cancel = LaravelFacturaMx::invoiceCanceller($invoice)
    ->setCancelTypeEnum(InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED)
    ->build();

// Estatus del CFDI ante el SAT (total = string EXACTO impreso en el CFDI)
$status = LaravelFacturaMx::status()
    ->setInvoice($invoice)
    ->setReceptorRfc('XAXX010101000')
    ->setTotal('612.90')
    ->build();
// $status->estado, $status->esCancelable, $status->invoiceStatusEnum (null si el SAT no lo reconoce)

// Recuperar el XML timbrado (get_xml y, si el PAC no lo indexa, fallback vía "stamped")
$xml = LaravelFacturaMx::RecoveryCfdiXmlFile()->setInvoice($invoice)->build();
```

### Manejo de errores

Todo fallo del paquete lanza `JiagBrody\LaravelFacturaMx\Exceptions\FacturaMxException` o una subclase:

| Excepción | Cuándo |
|---|---|
| `PacConnectionException` | Fallo de red/SOAP con el PAC. **Reintentable** sin riesgo de duplicar timbres. |
| `PacStampInProgressException` | El PAC sigue procesando el timbrado asíncrono; reintenta en unos segundos. |
| `PacUnexpectedResponseException` | Respuesta con estructura desconocida; revisa el log SOAP. |
| `InvoiceAlreadyStampedException` | La factura ya tiene CFDI; se evitó un doble timbrado. |
| `InvoiceNotStampedException` | La operación (cancelar/estatus/recuperar) requiere una factura timbrada. |
| `StaleCfdiDraftException` | El borrador quedó fuera de la ventana de 72 h; regenera el CFDI. |
| `CfdiPreValidationException` | El CFDI no pasó la validación local (incluye la lista de errores). |
| `InvoiceDocumentMissingException` | Falta el XML de borrador (registro o archivo físico). |

## Testing

```bash
composer test      # Pest
composer analyse   # PHPStan (larastan)
composer format    # Pint
```

Los tests que sellan un CFDI real se saltan si no encuentras el CSD de pruebas oficial del SAT (`EKU9003173C9`) en `sat_files_path` — ver `tests/Pest.php`. Los tests de contrato del PAC corren siempre: usan respuestas SOAP reales grabadas del demo de Finkok.

## Checklist para producción

1. `pac_environment_production => true` y credenciales productivas del PAC en `.env`.
2. CSD reales de los emisores en `sat_files_path` (carpeta fuera del acceso público).
3. `filesystem_disk` privado (los CFDI llevan datos personales).
4. Verifica en los primeros timbrados los logs `storage/logs/cfdi_finkok_*.log`.

## Changelog / Contributing / Security

Ver [CHANGELOG](CHANGELOG.md), [CONTRIBUTING](CONTRIBUTING.md) y la [política de seguridad](../../security/policy).

## Créditos y licencia

- [J. Israel Alvarez G. (Brody)](https://github.com/jiagbrody)
- Construido sobre [eclipxe/cfdiutils](https://github.com/eclipxe13/CfdiUtils) y [phpcfdi/credentials](https://github.com/phpcfdi/credentials).

The MIT License (MIT). Ver [License File](LICENSE.md).
