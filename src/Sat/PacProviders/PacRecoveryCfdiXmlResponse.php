<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

/**
 * Resultado inmutable de la recuperación de un XML previamente timbrado.
 */
final class PacRecoveryCfdiXmlResponse
{
    private function __construct(
        private readonly bool $checkProcess,
        private readonly string $xml,
    ) {}

    public static function found(string $xml): self
    {
        return new self(checkProcess: true, xml: $xml);
    }

    public static function notFound(): self
    {
        return new self(checkProcess: false, xml: '');
    }

    public function getCheckProcess(): bool
    {
        return $this->checkProcess;
    }

    public function getXml(): string
    {
        return $this->xml;
    }
}
