<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use App\Enums\InvoiceCompanyEnum;

/*TODO: HACER LAS PROPIEDADES "readonly"*/

final readonly class InvoiceCompanyHelper
{
    public string $rfc;

    public string $nombre;

    public string $regimenFiscal;

    public string $facAtrAdquirente;

    public string $pathCertificado;

    public string $pathKey;

    public string $passPhrase;

    public string $serialNumber;

    public string $pacEnvironment;

    public function __construct(public int $invoiceCompanyId)
    {
        $this->pacEnvironment = env('PAC_ENVIRONMENT');
        $enum                 = InvoiceCompanyEnum::from($invoiceCompanyId);
        $sat                  = $enum->getSatData();
        $certificates         = $enum->getCertificates();

        $this->rfc              = $sat['rfc'];
        $this->nombre           = $sat['nombre'];
        $this->regimenFiscal    = $sat['regimen_fiscal'];
        $this->facAtrAdquirente = $sat['fac_atr_adquirente'];
        $this->pathCertificado  = $certificates['path_certificado'];
        $this->pathKey          = $certificates['path_key'];
        $this->passPhrase       = $certificates['pass_phrase'];
        $this->serialNumber     = $certificates['serial_number'];

        return $this;
    }
}
