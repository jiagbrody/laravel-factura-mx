<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

readonly final class ConceptoAtributos
{
    use AtributosHelperTrait;

    private string $ClaveProdServ;

    private string $NoIdentificacion;

    private string $Cantidad;

    private string $ClaveUnidad;

    private string $Unidad;

    private string $Descripcion;

    private string $ValorUnitario;

    private string $Importe;

    private string $Descuento;

    private string $ObjetoImp;

    private ImpuestoTrasladoAtributos $impuestoTrasladoAtributos;

    public function setClaveProdServ(string $ClaveProdServ): void
    {
        $this->ClaveProdServ = $ClaveProdServ;
    }

    public function getClaveProdServ(): string
    {
        return $this->ClaveProdServ;
    }

    public function setCantidad(string $Cantidad): void
    {
        $this->Cantidad = $Cantidad;
    }

    public function getCantidad(): string
    {
        return $this->Cantidad;
    }

    public function setClaveUnidad(string $ClaveUnidad): void
    {
        $this->ClaveUnidad = $ClaveUnidad;
    }

    public function setNoIdentificacion(string $NoIdentificacion): void
    {
        $this->NoIdentificacion = $NoIdentificacion;
    }

    public function getNoIdentificacion(): string
    {
        return $this->NoIdentificacion;
    }

    public function setUnidad(string $Unidad): void
    {
        $this->Unidad = $Unidad;
    }

    public function setDescripcion(string $Descripcion): void
    {
        $this->Descripcion = $Descripcion;
    }

    public function getDescripcion(): string
    {
        return $this->Descripcion;
    }

    public function setValorUnitario(float $ValorUnitario): void
    {
        $this->ValorUnitario = (string)PatronDeDatosHelper::t_import($ValorUnitario);
    }

    public function getValorUnitario(): string
    {
        return $this->ValorUnitario;
    }

    public function getClaveUnidad(): string
    {
        return $this->ClaveUnidad;
    }

    public function getUnidad(): string
    {
        return $this->Unidad;
    }

    public function setImporte(float $Importe): void
    {
        $this->Importe = (string)PatronDeDatosHelper::t_import($Importe);
    }

    public function getImporte(): string
    {
        return $this->Importe;
    }

    public function setDescuento(float $Descuento): void
    {
        $this->Descuento = (string)PatronDeDatosHelper::t_import($Descuento);
    }

    public function getDescuento(): string
    {
        return $this->Descuento;
    }

    public function setObjetoImp(string $ObjetoImp): void
    {
        $this->ObjetoImp = $ObjetoImp;
    }

    public function getObjetoImp(): string
    {
        return $this->ObjetoImp;
    }

    public function setImpuestoTrasladoAtributos(ImpuestoTrasladoAtributos $impuestoTrasladoAtributos): void
    {
        $this->impuestoTrasladoAtributos = $impuestoTrasladoAtributos;
    }

    public function getImpuestoTrasladoAtributos(): ImpuestoTrasladoAtributos
    {
        return $this->impuestoTrasladoAtributos;
    }
}
