<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

/**
 * Contrato que debe cumplir cualquier PAC. Los builders del paquete solo
 * conocen esta interfaz; el proveedor concreto se resuelve vía
 * PacProviderFactory según el config "pac_chosen".
 */
interface ProviderPacInterface
{
    public function setInvoiceCompanyHelper(InvoiceCompany $company): void;

    /**
     * RFC del receptor, requerido para consultar estatus ante el SAT.
     */
    public function setReceptorRfc(string $receptorRfc): void;

    /**
     * Total EXACTO impreso en el CFDI (string recomendado, p. ej. "1234.50");
     * el SAT lo usa para localizar el comprobante.
     */
    public function setTotal(float|string $total): void;

    /*
     * Timbrar factura.
     */
    public function stampInvoice(): PacStampResponse;

    /**
     * Solicitud de cancelación ante el SAT.
     */
    public function cancelInvoice(InvoiceCfdiCancelTypeEnum $cfdiCancelTypeEnum, ?string $replacementUUID = null): PacCancelResponse;

    /*
     * Consultar el estatus del CFDI ante el SAT.
     */
    public function statusInvoice(): PacStatusResponse;

    /*
     * Recuperar un XML previamente timbrado.
     */
    public function getXmlStamped(): PacRecoveryCfdiXmlResponse;
}
