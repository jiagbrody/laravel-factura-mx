<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

use App\Enums\CfdiCancelTypeEnum;

interface ProviderPacInterface
{
    /*
     * Timbrar factura.
     *
     * https://wiki.finkok.com/doku.php?id=metodo_quick_stamp
     * https://wiki.finkok.com/doku.php?id=php#consumir_metodo_quick_stamp_del_web_service_de_timbrado_en_php
     */
    public function stampInvoice();

    /**
     * Solicitud de cancelar factura.
     * $type = 01, 02, 03, 04
     *
     * "Cancel_signature" siempre da error 205, ya que nunca funciona en ambiente DEMO por parte del SAT.
     * Asi que uso "sign_cancel" este metodo está simulado en Finkok en ambiente DEMO.
     * https://wiki.finkok.com/doku.php?id=sign_cancel2022
     * https://wiki.finkok.com/doku.php?id=php#consumir_web_service_de_cancel_signature
     * https://wiki.finkok.com/doku.php?id=errores-cancelacion
     */
    public function cancelInvoice(CfdiCancelTypeEnum $cfdiCancelTypeEnum, ?string $UUID): PacCancelResponse;

    /*
     * Checar estatus de la factura que se hizo solicitud de cancelar.
     *
     * https://wiki.finkok.com/doku.php?id=get_sat_status
     * https://wiki.finkok.com/doku.php?id=status_efos
     * https://wiki.finkok.com/doku.php?id=php#consumir_metodo_get_sat_status_del_web_service_de_cancelacion_en_php
     */
    public function checkStatusInvoice();
}
