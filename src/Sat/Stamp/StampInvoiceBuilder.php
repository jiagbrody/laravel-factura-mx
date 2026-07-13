<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Stamp;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacProviderFactory;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\ProviderPacInterface;

final class StampInvoiceBuilder
{
    protected readonly ProviderPacInterface $pacProvider;

    public function __construct(public readonly Invoice $invoice)
    {
        $this->pacProvider = PacProviderFactory::make($invoice);
    }

    public function build(): PacStampResponse
    {
        return $this->pacProvider->stampInvoice();
    }
}
