<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

class PacCancelResponse
{
    public bool $checkProcess;

    public string $uuid;

    public string $estatusUUID;

    public string $estatusCancelacion;

    public string $acuse;

    public string $date;

    public function setCheckProcess(bool $checkProcess): void
    {
        $this->checkProcess = $checkProcess;
    }

    public function getCheckProcess(): bool
    {
        return $this->checkProcess;
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function setEstatusUUID(string $estatusUUID): void
    {
        $this->estatusUUID = $estatusUUID;
    }

    public function setEstatusCancelacion(string $estatusCancelacion): void
    {
        $this->estatusCancelacion = $estatusCancelacion;
    }

    public function setAcuse(string $acuse): void
    {
        $this->acuse = $acuse;
    }

    /**
     * El SAT no devuelve acuse cuando el UUID ya estaba cancelado
     * (EstatusUUID 202), por lo que la propiedad puede quedar sin inicializar.
     */
    public function hasAcuse(): bool
    {
        return isset($this->acuse);
    }
}
