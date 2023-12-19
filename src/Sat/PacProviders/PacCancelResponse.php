<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

class PacCancelResponse
{
    public bool $checkProcess;

    public string $uuid;

    public string $estatusUUID;

    public string $estatusCancelacion;

    public string $acuse;

    public function __construct()
    {
        $this->checkProcess = false;
    }
}
