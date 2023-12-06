<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

class ComprobanteAtributos
{
    readonly public string $Version;

    public string $Serie;

    public string $Folio;

    readonly public string $Fecha;

    public string $Sello;

    public string $FormaPago;

    public string $NoCertificado;

    public string $Certificado;

    public string $CondicionesDePago;

    public string $SubTotal;

    public string $Descuento;

    public string $Moneda;

    public string $TipoCambio;

    public string $Total;

    readonly public string $TipoDeComprobante;

    public string $Exportacion;

    public string $MetodoPago;

    readonly public string $LugarExpedicion;

    public string $Confirmacion;

    public function __construct(string $tipoComprobante)
    {
        $this->Version           = '4.0';
        $this->TipoDeComprobante = $tipoComprobante;
        $this->LugarExpedicion   = '63732';
        $this->Fecha             = date('Y-m-d\\TH:i:s');
        $this->Exportacion       = '01';
    }
}
