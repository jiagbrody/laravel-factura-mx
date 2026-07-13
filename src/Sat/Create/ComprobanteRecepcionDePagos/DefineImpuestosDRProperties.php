<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos;

class DefineImpuestosDRProperties
{
    private const VALUE_OBJECT_IMP_WITH_TAX = '02';

    public const IMPUESTO_DR = '002';

    public string $BaseDR;

    public string $ImpuestoDR;

    public string $TipoFactorDR;

    public string $TasaOCuotaDR;

    public string $ImporteDR;

    public function setBaseDR($BaseDR): void
    {
        $this->BaseDR = $BaseDR;
    }

    public function getBaseDR(): string
    {
        return $this->BaseDR;
    }

    public function setImpuestoDR(string $ImpuestoDR): void
    {
        $this->ImpuestoDR = $ImpuestoDR;
    }

    public function getImpuestoDR(): string
    {
        return $this->ImpuestoDR;
    }

    public function setTipoFactorDR(string $TipoFactorDR): void
    {
        $this->TipoFactorDR = $TipoFactorDR;
    }

    public function getTipoFactorDR(): string
    {
        return $this->TipoFactorDR;
    }

    public function setTasaOCuotaDR(string $TasaOCuotaDR): void
    {
        $this->TasaOCuotaDR = $TasaOCuotaDR;
    }

    public function getTasaOCuotaDR(): string
    {
        return $this->TasaOCuotaDR;
    }

    public function setImporteDR($ImporteDR): void
    {
        $this->ImporteDR = $ImporteDR;
    }

    public function getImporteDR(): string
    {
        return $this->ImporteDR;
    }
}
