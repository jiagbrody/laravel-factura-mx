# Checklist de pruebas de timbrado (ambiente DEMO)

> Guion de aceptación de punta a punta, ejecutado desde la plataforma anfitriona contra el
> demo de Finkok. Reutilizable antes de cada release mayor y para la salida a producción.
>
> Estado al 2026-07-14 · paquete v2.1.0-dev

## Ingreso (factura)

- [x] **Vista previa** con receptor incompleto (build provisional, sin validación local) — ✓ 14/jul
- [x] **Timbrar PUE** — ✓ 14/jul
- [x] **Timbrar PPD** — ✓ 14/jul
- [x] **Reintento de timbrado** de una factura ya timbrada en el PAC → recupera UUID/XML
      originales vía `stamped` (error 307), sin duplicar — ✓ 13/jul (factura 2)
- [x] **Cancelar motivo 02** (con errores, sin relación) — ✓ 14/jul (factura 1: sign_cancel 201
      + acuse persistido; UI pasa a «en espera del SAT» y tras consultar estatus → Cancelado)
- [ ] **Cancelar motivo 01** (con errores, con relación) — requiere timbrar primero la
      factura **sustituta** y cancelar la original con su `FolioSustitucion`
- [ ] **Cancelar motivo 03** (no se llevó a cabo la operación)
- [x] **Reintentar cancelación** de una ya cancelada → 202 idempotente, sin error ni acuse duplicado
      — ✓ 14/jul con matiz: el DEMO re-simula 201 con el MISMO acuse (no devuelve 202), por lo que
      el reintento en vivo duplica el registro de acuse si se salta el candado de la plataforma
      (`hasCancellationInProcess`, que en flujos reales lo impide). El 202 productivo queda fijado
      por el test de contrato «202 previamente cancelado es éxito idempotente sin acuse».
- [x] **Consultar estatus** tras timbrar → «Vigente» (y datos EsCancelable/EFOS coherentes) — ✓ 14/jul
      (factura 1: Vigente, «Cancelable sin aceptación»; EFOS 100 es respuesta enlatada del demo)
- [x] **Consultar estatus** tras cancelar → transición hasta «Cancelado» — ✓ 14/jul (factura 1:
      Estado=Cancelado, EstatusCancelacion=«Cancelado sin aceptación»; la plataforma sincroniza el
      badge a Cancelado y oculta el botón de cancelar)
- [x] **PDF de la plataforma** correcto (cabeceras, complementoData, QR del SAT escaneable) — ✓ 14/jul
      (factura 1: QR decodificado → verificacfdi con id/re/rr/tt/fe correctos)

## Egreso (nota de crédito)

- [ ] **Vista previa** (build provisional)
- [x] **Timbrar** nota de crédito ligada a su factura origen (TipoRelacion) — ✓ 14/jul
- [ ] **Saldos**: la cuenta fiscal refleja la nota (lógica de la plataforma)
- [ ] **Cancelar** nota de crédito (motivo 02)
- [ ] **Consultar estatus** de la nota

## Pago (REP 2.0)

- [ ] **Vista previa / edición** del recibo (build provisional)
- [ ] **Timbrar REP** de un pago a factura PPD (un documento relacionado)
- [ ] **REP con parcialidades** (número de parcialidad y saldos DR correctos)
- [ ] **REP con varios documentos relacionados** (si el flujo lo permite)
- [ ] **Cancelar REP**
- [ ] **Saldos**: la cuenta fiscal refleja el pago (lógica de la plataforma)

## Sustitución (flujo completo)

- [ ] Timbrar factura B que **sustituye** a factura A (relación 04) → tras timbrar B, la
      plataforma cancela A con motivo 01 y FolioSustitucion = UUID de B (acción Mode-01)

## Transversales / resiliencia (varios ya cubiertos por tests de contrato)

- [x] Validación local atrapa CFDI inválido con mensaje claro y NO envía al PAC — ✓ 13/jul
      (incidente ObjetoImp)
- [x] Recuperación de XML: `get_xml` falla → fallback `stamped` con el borrador — ✓ 14/jul
      (factura 2, archivos regenerados)
- [x] Logs SOAP en `storage/logs/cfdi_finkok_*.log` con contraseña redactada — ✓ 13/jul
- [ ] Incidencia del PAC visible en la UI y registrada en `invoice_incidents`
- [ ] Borrador fuera de la ventana de 72 h → rechazo claro con `StaleCfdiDraftException`
      (truco para probar: baja `stamp_draft_max_age_hours` a 1 y usa un borrador de ayer)
- [ ] Timbrado con credenciales inválidas → error de PAC legible (no pantalla blanca)

## Para producción (cuando llegue el día)

- [ ] Credenciales productivas en `.env` + `pac_environment_production = true`
- [ ] CSD reales de los emisores en `sat_files_path`
- [ ] Primer timbrado real: verificar log SOAP, UUID en el portal del SAT y PDF/QR
- [ ] Verificar si `get_xml` productivo indexa correctamente (pregunta abierta del demo)
- [ ] Cancelación real de una factura de prueba interna
