<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

use App\Enums\CfdiCancelTypeEnum;

interface ProviderPacInterface
{
    /*
     * Timbrar factura.
     */
    public function stampInvoice();

    /**
     * Solicitud de cancelar factura.
     * $type = 01, 02, 03, 04
     */
    public function cancelInvoice(CfdiCancelTypeEnum $cfdiCancelTypeEnum, ?string $UUID): PacCancelResponse;

    /*
     * Checar estatus de la factura que se hizo solicitud de cancelar.
     */
    public function checkStatusInvoice();
}
