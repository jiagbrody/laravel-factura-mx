<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

final readonly class EmisorAtributos
{
    use AtributosHelperTrait;

    public string $Rfc;

    public string $Nombre;

    public string $RegimenFiscal;

    public string $FacAtrAdquirente;

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

    public function setRegimenFiscal(string $RegimenFiscal): void
    {
        $this->RegimenFiscal = $RegimenFiscal;
    }

    public function getRegimenFiscal(): string
    {
        return $this->RegimenFiscal;
    }

    public function setFacAtrAdquirente(string $FacAtrAdquirente): void
    {
        $this->FacAtrAdquirente = $FacAtrAdquirente;
    }

    public function getFacAtrAdquirente(): string
    {
        return $this->FacAtrAdquirente;
    }
}
