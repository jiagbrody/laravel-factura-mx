<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

readonly class PacStatusResponse
{
    public bool $checkProcess;

    public string $detallesValidacionEFOS;

    public string $validacionEFOS;

    public string $esCancelable;

    public string $codigoEstatus;

    public string $estado;

    public string $estatusCancelacion;

    public function setCheckProcess(bool $checkProcess): void
    {
        $this->checkProcess = $checkProcess;
    }

    public function setCodigoEstatus(string $codigoEstatus): void
    {
        $this->codigoEstatus = $codigoEstatus;
    }

    public function setDetallesValidacionEFOS(string $detallesValidacionEFOS): void
    {
        $this->detallesValidacionEFOS = $detallesValidacionEFOS;
    }

    public function setEsCancelable(string $esCancelable): void
    {
        $this->esCancelable = $esCancelable;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function setEstatusCancelacion(string $estatusCancelacion): void
    {
        $this->estatusCancelacion = $estatusCancelacion;
    }

    public function setValidacionEFOS(string $validacionEFOS): void
    {
        $this->validacionEFOS = $validacionEFOS;
    }
}
