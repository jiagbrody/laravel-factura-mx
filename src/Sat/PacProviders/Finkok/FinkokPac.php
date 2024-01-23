<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\ProviderPacInterface;

//use PhpCfdi\Finkok\FinkokEnvironment;
//use PhpCfdi\Finkok\FinkokSettings;
//use PhpCfdi\Finkok\QuickFinkok;

/*
 * https://wiki.finkok.com/doku.php?id=Inicio
 * https://wiki.finkok.com/doku.php?id=php&s[]=response&s[]=client&s[]=soapcall&s[]=sign_cancel&s[]=params
 * https://wiki.finkok.com/doku.php?id=cfdi40
 */

class FinkokPac implements ProviderPacInterface
{
    use CancelTrait, StampTrait, StatusTrait;

    protected string $pacEnvironment;

    protected string $usernameFinkok;

    protected string $passwordFinkok;

    protected string $stampUrlFinkok;

    protected string $cancelUrlFinkok;

    protected QuickFinkok $quickFinkok;

    public function __construct(protected Invoice $invoice)
    {

        $this->usernameFinkok = (string) config('factura-mx.pac_providers.finkok.user');
        $this->passwordFinkok = (string) config('factura-mx.pac_providers.finkok.password');

        if (config('factura-mx.pac_environment_production')) {
            $this->pacEnvironment = 'production';
            $this->stampUrlFinkok = '';
            $this->cancelUrlFinkok = '';
        } else {
            $this->pacEnvironment = 'development';
            $this->stampUrlFinkok = 'https://demo-facturacion.finkok.com/servicios/soap/stamp.wsdl';
            $this->cancelUrlFinkok = 'https://demo-facturacion.finkok.com/servicios/soap/cancel.wsdl';
        }

        //$settings = null;
        //if ($this->pacEnvironment === 'development') {
        //    $settings = new FinkokSettings($this->usernameFinkok, $this->passwordFinkok,
        //        FinkokEnvironment::makeDevelopment());
        //} elseif ($this->pacEnvironment === 'production') {
        //    $settings = new FinkokSettings($this->usernameFinkok, $this->passwordFinkok,
        //        FinkokEnvironment::makeProduction());
        //}
        //$this->quickFinkok = new QuickFinkok($settings);
    }

    /*
     * Timbrar factura.
     *
     * https://wiki.finkok.com/doku.php?id=metodo_quick_stamp
     * https://wiki.finkok.com/doku.php?id=php#consumir_metodo_quick_stamp_del_web_service_de_timbrado_en_php
     */
    public function stampInvoice(): PacStampResponse
    {
        return $this->stamp();
    }

    /**
     * Solicitud de cancelar factura.
     * $type = 01, 02, 03, 04
     *
     * "Cancel_signature" siempre da error 205, ya que nunca funciona en ambiente DEMO por parte del SAT.
     * Asi que uso "sign_cancel" este método está simulado en Finkok en ambiente DEMO.
     * https://wiki.finkok.com/doku.php?id=sign_cancel2022
     * https://wiki.finkok.com/doku.php?id=php#consumir_web_service_de_cancel_signature
     * https://wiki.finkok.com/doku.php?id=errores-cancelacion
     */
    public function cancelInvoice($cfdiCancelTypeEnum, $UUID): PacCancelResponse
    {
        return $this->cancel($cfdiCancelTypeEnum, $UUID);
    }

    /*
     * Checar estatus de la factura que se hizo solicitud de cancelar.
     *
     * https://wiki.finkok.com/doku.php?id=get_sat_status
     * https://wiki.finkok.com/doku.php?id=status_efos
     * https://wiki.finkok.com/doku.php?id=php#consumir_metodo_get_sat_status_del_web_service_de_cancelacion_en_php
     */
    public function checkStatusInvoice(): array
    {
        return $this->getStatusCfdiSat();
    }
}
