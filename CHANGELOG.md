# Changelog

All notable changes to `laravel-factura-mx` will be documented in this file.

## v2.1.0 - 2026-07-14

### Añadido

- `build(validate: false)` en `GenericCreator` y `PagoCreator`: permite construir CFDI
  **provisionales** sin validación local para esa llamada (p. ej. vistas previas con el
  receptor aún incompleto). Con `null` (default) manda el config `pre_validate_cfdi`.
- Tests de contrato para cancelación (`sign_cancel`: 201 con acuse, 202 idempotente,
  rechazo con incidente, motivo 01 con FolioSustitucion) y consulta de estatus
  (`get_sat_status`: mapeo Vigente/Cancelado/desconocido y campos EFOS), construidos con
  respuestas reales grabadas del demo de Finkok.

### Cambiado

- `PagoCreator::build()` ahora aplica la validación local de verdad: antes ejecutaba
  `validate()` e ignoraba el resultado; ahora los errores lanzan `CfdiPreValidationException`
  igual que el creator genérico (misma bandera/config para omitirla).

## v2.0.0 - 2026-07-14

Reescritura mayor del núcleo del paquete (CFDI 4.0). Cambios con ruptura respecto a la línea 0.x.

### Añadido

- Validación local del CFDI (XSD + reglas SAT vía cfdiutils) antes de enviar al PAC (`pre_validate_cfdi`).
- Guard de la ventana de 72 horas del SAT para borradores (`stamp_draft_max_age_hours`) y guard anti-doble-timbrado.
- Manejo completo de los estados reales de Finkok: error 307 ("timbrado previamente", cuyo UUID de nivel superior es el WorkProcessId), timbrado asíncrono en cola, y recuperación del resultado original vía la operación `stamped`.
- Recuperación de XML con cascada: `get_xml` por UUID y fallback vía `stamped` con el borrador.
- Excepciones tipadas (`FacturaMxException` y subclases) en lugar de `abort()`/`\Exception` — seguras para colas y comandos.
- Transporte SOAP central (`FinkokSoapCaller`): timeouts configurables, log de request/response también en fallo, y contraseña del PAC redactada en logs.
- Persistencia post-timbrado integrada y opcional (`persist_stamp_result`): estatus, CFDI/UUID, XML, PDF y limpieza de borradores en la misma llamada.
- Generación del PDF legible con el QR oficial de verificación del SAT; vista configurable (`pdf_invoice_view`) y desactivable (`generate_pdf_on_stamp`).
- `PacProviderFactory` que honra `pac_chosen` sobre un `ProviderPacInterface` completo y tipado.
- URLs oficiales de Finkok para demo y producción incluidas por default.
- Tests de contrato del PAC con respuestas SOAP reales grabadas; suite corriendo en CI para PHP 8.2/8.3/8.4.
- `user_id` nullable en facturas, CFDIs e incidentes (timbrado desde colas).

### Cambiado

- DTOs de respuesta del PAC inmutables con constructores nombrados; los getters son seguros en cualquier estado.
- El estatus del SAT "No Encontrado" ya no se mapea a borrador (enum `null` cuando no hay equivalente local).
- Disco de almacenamiento default privado (`local`) — los CFDI contienen datos personales.
- La `Fecha` del comprobante se genera en la zona horaria configurada; el paquete ya no muta la zona global de PHP del host.
- Config del paquete fusionada correctamente vía Composer (antes requería copiar el archivo al host).
- Migraciones portables (fresh builds en SQLite/PostgreSQL/MySQL) y sin sembrar emisores de prueba del SAT.

### Eliminado

- La UI web opcional (Inertia/Vue), sus rutas y controladores.
- El código muerto acoplado al app de origen (`App\*`) y el morph map global forzado.

## 0.0.1

Versión inicial experimental.
