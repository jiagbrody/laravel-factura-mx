<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Status;

use JiagBrody\LaravelFacturaMx\Exceptions\FacturaMxException;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacProviderFactory;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStatusResponse;

final class StatusInvoiceBuilder
{
    protected Invoice $invoice;

    protected string $receptorRfc;

    protected string $total;

    public function setInvoice(Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Conservado por compatibilidad: el proveedor ahora se resuelve en
     * build(), por lo que el orden de los setters ya no importa.
     */
    public function setPacProvider(): self
    {
        return $this;
    }

    public function setReceptorRfc(string $receptorRfc): self
    {
        $this->receptorRfc = $receptorRfc;

        return $this;
    }

    /**
     * Total EXACTO impreso en el CFDI (string recomendado, p. ej. "1234.50").
     */
    public function setTotal(float|string $total): self
    {
        $this->total = is_float($total) ? number_format($total, 2, '.', '') : $total;

        return $this;
    }

    public function build(): PacStatusResponse
    {
        if (! isset($this->invoice)) {
            throw new FacturaMxException('Llama a setInvoice() antes de build().');
        }

        if (! isset($this->receptorRfc) || ! isset($this->total)) {
            throw new FacturaMxException('Llama a setReceptorRfc() y setTotal() antes de build(): el SAT los requiere para localizar el CFDI.');
        }

        $pacProvider = PacProviderFactory::make($this->invoice);
        $pacProvider->setReceptorRfc($this->receptorRfc);
        $pacProvider->setTotal($this->total);

        return $pacProvider->statusInvoice();
    }
}
