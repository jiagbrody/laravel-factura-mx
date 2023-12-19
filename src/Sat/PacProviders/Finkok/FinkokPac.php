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
        $this->pacEnvironment = (string) env('PAC_ENVIRONMENT');
        $this->usernameFinkok = (string) env('PAC_FINKOK_USER');
        $this->passwordFinkok = (string) env('PAC_FINKOK_PASSWORD');
        $this->stampUrlFinkok = (string) env('PAC_FINKOK_STAMP_URL');
        $this->cancelUrlFinkok = (string) env('PAC_FINKOK_CANCEL_URL');

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

    public function stampInvoice(): PacStampResponse
    {
        return $this->stamp();
    }

    public function cancelInvoice($cfdiCancelTypeEnum, $UUID): PacCancelResponse
    {
        return $this->cancel($cfdiCancelTypeEnum, $UUID);
    }

    public function checkStatusInvoice(): array
    {
        return $this->getStatusCfdiSat();
    }
}
