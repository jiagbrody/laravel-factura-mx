<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

readonly final class ComprobanteAtributos
{
    use AtributosHelperTrait;

    private string $Version;

    private string $Serie;

    private string $Folio;

    private string $Fecha;

    private string $Sello;

    protected string $FormaPago;

    private string $NoCertificado;

    private string $Certificado;

    private string $CondicionesDePago;

    private string $SubTotal;

    private string $Descuento;

    private string $Moneda;

    private string $TipoCambio;

    private string $Total;

    private string $TipoDeComprobante;

    private string $Exportacion;

    private string $MetodoPago;

    private string $LugarExpedicion;

    public string $Confirmacion;

    public function __construct()
    {
        $this->Version = '4.0';
        $this->Fecha   = date('Y-m-d\\TH:i:s');
    }

    // public function setVersion(string $Version): void
    // {
    //     $this->Version = $Version;
    // }

    public function getVersion(): string
    {
        return $this->Version;
    }

    public function setSerie(string $Serie): void
    {
        $this->Serie = $Serie;
    }

    public function getSerie(): string
    {
        return $this->Serie;
    }

    public function setFolio(string $Folio): void
    {
        $this->Folio = $Folio;
    }

    public function getFolio(): string
    {
        return $this->Folio;
    }

    // public function setFecha(string $Fecha): void
    // {
    //     $this->Fecha = $Fecha;
    // }

    public function getFecha(): string
    {
        return $this->Fecha;
    }

    public function setSello(string $Sello): void
    {
        $this->Sello = $Sello;
    }

    public function getSello(): string
    {
        return $this->Sello;
    }

    public function setFormaPago(string $FormaPago): void
    {
        $this->FormaPago = $FormaPago;
    }

    public function getFormaPago(): null|string
    {
        return $this->FormaPago ?? null;
    }

    public function setNoCertificado(string $NoCertificado): void
    {
        $this->NoCertificado = $NoCertificado;
    }

    public function getNoCertificado(): string
    {
        return $this->NoCertificado;
    }

    public function setCertificado(string $Certificado): void
    {
        $this->Certificado = $Certificado;
    }

    public function getCertificado(): string
    {
        return $this->Certificado;
    }

    public function setCondicionesDePago(string $CondicionesDePago): void
    {
        $this->CondicionesDePago = $CondicionesDePago;
    }

    public function getCondicionesDePago(): null|string
    {
        return $this->CondicionesDePago ?? null;
    }

    public function setSubTotal(float $SubTotal): void
    {
        $this->SubTotal = (string)PatronDeDatosHelper::t_import($SubTotal);
    }

    public function getSubTotal(): null|string
    {
        return $this->SubTotal ?? null;
    }

    public function setDescuento(float $Descuento): void
    {
        $this->Descuento = (string)PatronDeDatosHelper::t_import($Descuento);
    }

    public function getDescuento(): null|string
    {
        return $this->Descuento ?? null;
    }

    public function setMoneda(string $Moneda): void
    {
        $this->Moneda = $Moneda;
    }

    public function getMoneda(): null|string
    {
        return $this->Moneda ?? null;
    }

    public function setTipoCambio(string $TipoCambio): void
    {
        $this->TipoCambio = $TipoCambio;
    }

    public function getTipoCambio(): null|string
    {
        return $this->TipoCambio ?? null;
    }

    public function setTotal(float $Total): void
    {
        $this->Total = (string)PatronDeDatosHelper::t_import($Total);
    }

    public function getTotal(): null|string
    {
        return $this->Total ?? null;
    }

    public function setTipoDeComprobante(string $TipoDeComprobante): void
    {
        $this->TipoDeComprobante = $TipoDeComprobante;
    }

    public function getTipoDeComprobante(): null|string
    {
        return $this->TipoDeComprobante ?? null;
    }

    public function setExportacion(string $Exportacion): void
    {
        $this->Exportacion = $Exportacion;
    }

    public function getExportacion(): null|string
    {
        return $this->Exportacion ?? null;
    }

    public function setMetodoPago(string $MetodoPago): void
    {
        $this->MetodoPago = $MetodoPago;
    }

    public function getMetodoPago(): null|string
    {
        return $this->MetodoPago ?? null;
    }

    public function setLugarExpedicion(string $LugarExpedicion): void
    {
        $this->LugarExpedicion = $LugarExpedicion;
    }

    public function getLugarExpedicion(): null|string
    {
        return $this->LugarExpedicion ?? null;
    }

    public function setConfirmacion(string $Confirmacion): void
    {
        $this->Confirmacion = $Confirmacion;
    }

    public function getConfirmacion(): string
    {
        return $this->Confirmacion;
    }


}
