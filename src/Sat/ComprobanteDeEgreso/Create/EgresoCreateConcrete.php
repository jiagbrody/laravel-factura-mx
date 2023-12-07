<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Create;

use App\Enums\InvoiceCompanyEnum;
use App\Services\CurrencyExchangeRateService;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
use CfdiUtils\Certificado\Certificado;

class EgresoCreateConcrete extends CfdiHelperAbstract
{
    const OBJETO_IMP_VERIFY_IS_TRASLADO = '02';
    protected ComprobanteAtributos $atributos;

    protected CurrencyExchangeRateService $currencyService;

    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
        $this->atributos       = new ComprobanteAtributos('E');
        $this->currencyService = new CurrencyExchangeRateService;
        parent::__construct($invoiceCompanyEnum);
    }

    public function addAtributos(array $atributos): self
    {
        $this->atributos->Serie         = $atributos['Serie'] ?? '';
        $this->atributos->Folio         = $atributos['Folio'] ?? '';
        $this->atributos->Sello         = '';
        $this->atributos->NoCertificado = '';
        $this->atributos->Certificado   = '';
        $this->atributos->Descuento     = $atributos['Descuento'] ?? "0";
        $this->atributos->FormaPago     = $atributos['FormaPago'] ?? "";
        $this->atributos->MetodoPago    = $atributos['MetodoPago'] ?? "PUE";
        $this->atributos->SubTotal      = $atributos['SubTotal'] ?? "0";
        $this->atributos->Moneda        = $atributos['Moneda'] ?? "0";
        $this->atributos->TipoCambio    = $atributos['TipoCambio'] ?? "1";
        $this->atributos->Total         = $atributos['Total'] ?? "0";
        $this->atributos->Exportacion   = "01";

        $this->creatorCfdi->comprobante()->addAttributes((array)$this->atributos);

        return $this;
    }

    public function addConceptos($products): self
    {
        $this->conceptoAtributos->NoIdentificacion = (string)$products['NoIdentificacion'];
        $this->conceptoAtributos->ClaveProdServ    = $products['ClaveProdServ'];
        $this->conceptoAtributos->Cantidad         = $products['Cantidad'];
        $this->conceptoAtributos->ClaveUnidad      = $products['ClaveUnidad'];
        $this->conceptoAtributos->Descuento        = $products['Descuento'];
        $this->conceptoAtributos->Descripcion      = (string)$products['Descripcion'];
        $this->conceptoAtributos->ValorUnitario    = (string)$products['ValorUnitario'];
        $this->conceptoAtributos->Importe          = (string)$products['Importe'];
        $this->conceptoAtributos->ObjetoImp        = (string)$products['ObjetoImp'];

        if ($this->conceptoAtributos->ObjetoImp === self::OBJETO_IMP_VERIFY_IS_TRASLADO) {
            $traslado             = new ImpuestoTrasladoAtributos();
            $traslado->Base       = $this->conceptoAtributos->Importe;
            $traslado->Impuesto   = $products['Impuestos']['Traslados']['Traslado'][0]['Impuesto'] ?? '002';
            $traslado->TipoFactor = $products['Impuestos']['Traslados']['Traslado'][0]['TipoFactor'] ?? 'Tasa';
            $traslado->TasaOCuota = $products['Impuestos']['Traslados']['Traslado'][0]['TasaOCuota'] ?? '0.160000';
            $traslado->Importe    = (string)(floatval($this->conceptoAtributos->Importe) * floatval($traslado->TasaOCuota));
            $this->creatorCfdi->comprobante()->addConcepto((array)$this->conceptoAtributos)->addTraslado((array)$traslado);
        } else {
            $this->creatorCfdi->comprobante()->addConcepto((array)$this->conceptoAtributos);
        }

        return $this;
    }

    public function build(): EgresoCreateBuild
    {
        $this->creatorCfdi->putCertificado(new Certificado($this->credential->certificate()->pem()), false);
        $this->creatorCfdi->addSumasConceptos(null, 2);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();

        return (new EgresoCreateBuild($this->credential, $this->creatorCfdi, $this->companyHelper));
    }
}
