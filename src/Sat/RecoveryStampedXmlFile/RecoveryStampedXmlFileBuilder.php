<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\RecoveryStampedXmlFile;

use JiagBrody\LaravelFacturaMx\Exceptions\FacturaMxException;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacProviderFactory;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacRecoveryCfdiXmlResponse;

class RecoveryStampedXmlFileBuilder
{
    protected Invoice $invoice;

    public function setInvoice(Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Conservado por compatibilidad: el proveedor ahora se resuelve en build().
     */
    public function setPacProvider(): self
    {
        return $this;
    }

    public function build(): PacRecoveryCfdiXmlResponse
    {
        if (! isset($this->invoice)) {
            throw new FacturaMxException('Llama a setInvoice() antes de build().');
        }

        return PacProviderFactory::make($this->invoice)->getXmlStamped();
    }
}
