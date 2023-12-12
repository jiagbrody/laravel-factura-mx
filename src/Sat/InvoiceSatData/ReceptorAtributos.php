<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

readonly final class ReceptorAtributos
{
    use AtributosHelperTrait;
    private string $Rfc;

    private string $Nombre;

    private string $DomicilioFiscalReceptor;

    private string $ResidenciaFiscal;

    private string $NumRegIdTrib;

    private string $RegimenFiscalReceptor;

    private string $UsoCFDI;

    public function setRfc(string $Rfc): void
    {
        $this->Rfc = $Rfc;
    }

    public function getRfc(): string
    {
        return $this->Rfc;
    }

    public function setNombre(string $Nombre): void
    {
        $this->Nombre = $Nombre;
    }

    public function getNombre(): string
    {
        return $this->Nombre;
    }

    public function setDomicilioFiscalReceptor(string $DomicilioFiscalReceptor): void
    {
        $this->DomicilioFiscalReceptor = $DomicilioFiscalReceptor;
    }

    public function getDomicilioFiscalReceptor(): string
    {
        return $this->DomicilioFiscalReceptor;
    }

    public function setResidenciaFiscal(string $ResidenciaFiscal): void
    {
        $this->ResidenciaFiscal = $ResidenciaFiscal;
    }

    public function getResidenciaFiscal(): string
    {
        return $this->ResidenciaFiscal;
    }

    public function setNumRegIdTrib(string $NumRegIdTrib): void
    {
        $this->NumRegIdTrib = $NumRegIdTrib;
    }

    public function getNumRegIdTrib(): string
    {
        return $this->NumRegIdTrib;
    }

    public function setRegimenFiscalReceptor(string $RegimenFiscalReceptor): void
    {
        $this->RegimenFiscalReceptor = $RegimenFiscalReceptor;
    }

    public function getRegimenFiscalReceptor(): string
    {
        return $this->RegimenFiscalReceptor;
    }

    public function setUsoCFDI(string $UsoCFDI): void
    {
        $this->UsoCFDI = $UsoCFDI;
    }

    public function getUsoCFDI(): string
    {
        return $this->UsoCFDI;
    }
}
