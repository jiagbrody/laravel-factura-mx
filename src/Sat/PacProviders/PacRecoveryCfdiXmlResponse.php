<?php

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

final readonly class PacRecoveryCfdiXmlResponse
{
    private bool $checkProcess;

    private string $xml;

    public function setCheckProcess(bool $checkProcess): void
    {
        $this->checkProcess = $checkProcess;
    }

    public function getCheckProcess(): bool
    {
        return $this->checkProcess;
    }

    public function setXml(string $xml): void
    {
        $this->xml = $xml;
    }

    public function getXml(): string
    {
        return $this->xml;
    }
}
