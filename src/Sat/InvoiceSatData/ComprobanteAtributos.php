<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

final readonly class ComprobanteAtributos
{
    use AtributosHelperTrait;

    private string $Version;

    private string $Serie;

    private string $Folio;

    private string $Fecha;

    private string $Sello;

    private string $FormaPago;

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
        $this->Fecha = date('Y-m-d\\TH:i:s');
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

    public function getFormaPago(): string
    {
        return $this->FormaPago;
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

    public function getCondicionesDePago(): string
    {
        return $this->CondicionesDePago;
    }

    public function setSubTotal(string $SubTotal): void
    {
        $this->SubTotal = $SubTotal;
    }

    public function getSubTotal(): string
    {
        return $this->SubTotal;
    }

    public function setDescuento(string $Descuento): void
    {
        $this->Descuento = $Descuento;
    }

    public function getDescuento(): string
    {
        return $this->Descuento;
    }

    public function setMoneda(string $Moneda): void
    {
        $this->Moneda = $Moneda;
    }

    public function getMoneda(): string
    {
        return $this->Moneda;
    }

    public function setTipoCambio(string $TipoCambio): void
    {
        $this->TipoCambio = $TipoCambio;
    }

    public function getTipoCambio(): string
    {
        return $this->TipoCambio;
    }

    public function setTotal(string $Total): void
    {
        $this->Total = $Total;
    }

    public function getTotal(): string
    {
        return $this->Total;
    }

    public function setTipoDeComprobante(string $TipoDeComprobante): void
    {
        $this->TipoDeComprobante = $TipoDeComprobante;
    }

    public function getTipoDeComprobante(): string
    {
        return $this->TipoDeComprobante;
    }

    public function setExportacion(string $Exportacion): void
    {
        $this->Exportacion = $Exportacion;
    }

    public function getExportacion(): string
    {
        return $this->Exportacion;
    }

    public function setMetodoPago(string $MetodoPago): void
    {
        $this->MetodoPago = $MetodoPago;
    }

    public function getMetodoPago(): string
    {
        return $this->MetodoPago;
    }

    public function setLugarExpedicion(string $LugarExpedicion): void
    {
        $this->LugarExpedicion = $LugarExpedicion;
    }

    public function getLugarExpedicion(): string
    {
        return $this->LugarExpedicion;
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
