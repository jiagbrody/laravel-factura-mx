<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\ExampleData\FinkokTestDataResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacRecoveryCfdiXmlResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacSoapCallerInterface;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStatusResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\ProviderPacInterface;

/*
 * https://wiki.finkok.com/doku.php?id=Inicio
 * https://wiki.finkok.com/doku.php?id=cfdi40
 */

class FinkokPac implements ProviderPacInterface
{
    use CancelTrait, StampTrait, StatusTrait, XmlStampedTrait;

    /**
     * URLs oficiales de Finkok por entorno. Pueden sobreescribirse desde
     * config "pac_providers.finkok.urls.{production|development}" sin tocar
     * código (por ejemplo, si Finkok publica nuevos endpoints).
     */
    private const DEFAULT_URLS = [
        'production' => [
            'stamp' => 'https://facturacion.finkok.com/servicios/soap/stamp.wsdl',
            'cancel' => 'https://facturacion.finkok.com/servicios/soap/cancel.wsdl',
            'utilities' => 'https://facturacion.finkok.com/servicios/soap/utilities.wsdl',
        ],
        'development' => [
            'stamp' => 'https://demo-facturacion.finkok.com/servicios/soap/stamp.wsdl',
            'cancel' => 'https://demo-facturacion.finkok.com/servicios/soap/cancel.wsdl',
            'utilities' => 'https://demo-facturacion.finkok.com/servicios/soap/utilities.wsdl',
        ],
    ];

    public readonly InvoiceCompanyHelper $invoiceCompanyHelper;

    public string $receptorRfc;

    public string $total;

    protected string $usernameFinkok;

    protected string $passwordFinkok;

    protected string $stampUrlFinkok;

    protected string $cancelUrlFinkok;

    protected string $statusUrlFinkok;

    protected string $utilitiesUrlFinkok;

    private ?PacSoapCallerInterface $soapCallerOverride = null;

    public function __construct(protected Invoice $invoice)
    {
        $this->usernameFinkok = (string) config('jiagbrody-laravel-factura-mx.pac_providers.finkok.user');
        $this->passwordFinkok = (string) config('jiagbrody-laravel-factura-mx.pac_providers.finkok.password');

        $environment = config('jiagbrody-laravel-factura-mx.pac_environment_production') ? 'production' : 'development';
        $configuredUrls = (array) config('jiagbrody-laravel-factura-mx.pac_providers.finkok.urls.'.$environment, []);

        $this->stampUrlFinkok = (string) ($configuredUrls['stamp'] ?? self::DEFAULT_URLS[$environment]['stamp']);
        $this->cancelUrlFinkok = (string) ($configuredUrls['cancel'] ?? self::DEFAULT_URLS[$environment]['cancel']);
        $this->utilitiesUrlFinkok = (string) ($configuredUrls['utilities'] ?? self::DEFAULT_URLS[$environment]['utilities']);

        // El método get_sat_status vive en el web service de cancelación de Finkok.
        $this->statusUrlFinkok = $this->cancelUrlFinkok;
    }

    public function setInvoiceCompanyHelper(InvoiceCompany $company): void
    {
        $this->invoiceCompanyHelper = new InvoiceCompanyHelper($company);
    }

    public function setReceptorRfc(string $receptorRfc): void
    {
        $this->receptorRfc = $receptorRfc;
    }

    /**
     * El servicio de consulta del SAT compara el total contra el impreso en
     * el CFDI; debe enviarse el string exacto del comprobante (p. ej. "1234.50").
     */
    public function setTotal(float|string $total): void
    {
        $this->total = is_float($total) ? number_format($total, 2, '.', '') : $total;
    }

    public function getStampTestData(): FinkokTestDataResponse
    {
        return new FinkokTestDataResponse;
    }

    /**
     * Permite inyectar un transporte SOAP alternativo (tests de contrato).
     */
    public function setSoapCaller(PacSoapCallerInterface $soapCaller): void
    {
        $this->soapCallerOverride = $soapCaller;
    }

    protected function soapCaller(): PacSoapCallerInterface
    {
        return $this->soapCallerOverride
            ?? new FinkokSoapCaller((int) config('jiagbrody-laravel-factura-mx.pac_soap_timeout_seconds', 30));
    }

    /*
     * Timbrar factura.
     *
     * https://wiki.finkok.com/doku.php?id=wsdl_stamp
     * Validador de Cfdi: https://validador.finkok.com
     */
    public function stampInvoice(): PacStampResponse
    {
        return $this->stamp();
    }

    /**
     * Solicitud de cancelar factura ($type = 01, 02, 03, 04).
     *
     * "Cancel_signature" siempre da error 205 en ambiente DEMO por parte del SAT,
     * así que se usa "sign_cancel", que sí está simulado en el DEMO de Finkok.
     * https://wiki.finkok.com/doku.php?id=sign_cancel2022
     * https://wiki.finkok.com/doku.php?id=errores-cancelacion
     */
    public function cancelInvoice(InvoiceCfdiCancelTypeEnum $cfdiCancelTypeEnum, ?string $replacementUUID = null): PacCancelResponse
    {
        return $this->cancel($cfdiCancelTypeEnum, $replacementUUID);
    }

    /*
     * Checar estatus de la factura ante el SAT.
     *
     * https://wiki.finkok.com/doku.php?id=get_sat_status
     */
    public function statusInvoice(): PacStatusResponse
    {
        return $this->getStatusCfdiSat();
    }

    /*
     * Devuelve un XML previamente timbrado (el tiempo de resguardo para rescatarlo es menor a 3 meses).
     *
     * https://wiki.finkok.com/en/home/webservices/utilerias/get_xml
     */
    public function getXmlStamped(): PacRecoveryCfdiXmlResponse
    {
        return $this->getXmlStampedCfdiSat();
    }
}
