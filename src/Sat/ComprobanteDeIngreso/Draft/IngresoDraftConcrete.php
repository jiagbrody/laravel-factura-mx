<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Draft;

use App\Enums\InvoiceCompanyEnum;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;
use CfdiUtils\Certificado\Certificado;
use CfdiUtils\Elements\ImpLocal10\ImpuestosLocales;

class IngresoDraftConcrete extends CfdiHelperAbstract
{
    protected ComprobanteAtributos $atributos;

    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
        $this->atributos = new ComprobanteAtributos('I');
        parent::__construct($invoiceCompanyEnum);
    }

    public function addAtributos(array $attributes): self
    {
        if (isset($attributes['Serie'])) {
            $this->atributos->Serie = $attributes['Serie'];
        }

        if (isset($attributes['Folio'])) {
            $this->atributos->Folio = $attributes['Folio'];
        }

        if (isset($attributes['Sello'])) {
            $this->atributos->Sello = $attributes['Sello'];
        }

        if (isset($attributes['FormaPago'])) {
            $this->atributos->FormaPago = $attributes['FormaPago'];
        }

        if (isset($attributes['NoCertificado'])) {
            $this->atributos->NoCertificado = $attributes['NoCertificado'];
        }

        if (isset($attributes['Certificado'])) {
            $this->atributos->Certificado = $attributes['Certificado'];
        }

        if (isset($attributes['CondicionesDePago'])) {
            $this->atributos->CondicionesDePago = $attributes['CondicionesDePago'];
        }

        if (isset($attributes['SubTotal'])) {
            $this->atributos->SubTotal = $attributes['SubTotal'];
        }

        if (isset($attributes['Descuento'])) {
            $this->atributos->Descuento = $attributes['Descuento'];
        }

        if (isset($attributes['Moneda'])) {
            $this->atributos->Moneda = $attributes['Moneda'];
        }

        if (isset($attributes['TipoCambio'])) {
            $this->atributos->TipoCambio = $attributes['TipoCambio'];
        }

        if (isset($attributes['Total'])) {
            $this->atributos->Total = $attributes['Total'];
        }

        if (isset($attributes['Exportacion'])) {
            $this->atributos->Exportacion = $attributes['Exportacion'];
        }

        if (isset($attributes['MetodoPago'])) {
            $this->atributos->MetodoPago = $attributes['MetodoPago'];
        }

        if (isset($attributes['Confirmacion'])) {
            $this->atributos->Confirmacion = $attributes['Confirmacion'];
        }


        if (empty($attributes['CondicionesDePago'])) {
            unset($this->atributos->CondicionesDePago);
        }

        if ($this->atributos->Moneda === 'MXN' || $this->atributos->Moneda === 'XXX') {
            unset($this->atributos->TipoCambio);
        }

        if (empty($attributes['Confirmacion'])) {
            unset($this->atributos->Confirmacion);
        }

        $this->creatorCfdi->comprobante()->addAttributes((array)$this->atributos);

        return $this;
    }

    public function addComplementoImpuestosLocales(array $impuestosLocales, float $total): self
    {
        if (!empty($impuestosLocales)) {
            $impLocales = new ImpuestosLocales();
            if (!empty($impuestosLocales['RetencionesLocales'])) {
                foreach ($impuestosLocales['RetencionesLocales'] as $retencion) {
                    $impLocales->addRetencionLocal([
                        'ImpLocRetenido'  => $retencion['ImpLocRetenido'],
                        'TasadeRetencion' => $retencion['TasadeRetencion'],
                        'Importe'         => $retencion['Importe'],
                    ]);
                }
                $this->creatorCfdi->comprobante()->addComplemento($impLocales);
            }
        }

        return $this;
    }

    public function build(): IngresoDraftBuild
    {
        $this->creatorCfdi->putCertificado(new Certificado($this->credential->certificate()->pem()), false);
        $this->creatorCfdi->addSumasConceptos(null, 2);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();

        return (new IngresoDraftBuild($this->credential, $this->creatorCfdi, $this->companyHelper));
    }
}
