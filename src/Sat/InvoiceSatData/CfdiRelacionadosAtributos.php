<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

use Illuminate\Support\Collection;

final readonly class CfdiRelacionadosAtributos
{
    use AtributosHelperTrait;

    public string $tipoRelacion;

    public Collection $cfdiRelacionado;

    public function __construct()
    {
        $this->cfdiRelacionado = collect();
    }

    public function setTipoRelacion(string $tipoRelacion): void
    {
        $this->tipoRelacion = $tipoRelacion;
    }

    public function getTipoRelacion(): string
    {
        return $this->tipoRelacion;
    }

    public function setPushCfdiRelacionado(Collection $cfdiRelacionado): void
    {
        $this->cfdiRelacionado->push($cfdiRelacionado);
    }

    public function pushCfdiRelacionado()
    {
        $this->cfdiRelacionado->push();
    }

    public function getCfdiRelacionado(): Collection
    {
        return $this->cfdiRelacionado;
    }
}
