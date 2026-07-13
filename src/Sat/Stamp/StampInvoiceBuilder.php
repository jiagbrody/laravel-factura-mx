<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Stamp;

use JiagBrody\LaravelFacturaMx\Actions\UpdateRecordsWhenStampingRevenueInvoiceAction;
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

    /**
     * Timbra ante el PAC y, si el timbrado procede y "persist_stamp_result"
     * está activo (default), persiste el resultado (estatus, CFDI/UUID, XML,
     * PDF y limpieza de borradores) en la misma llamada — simétrico con
     * CancelInvoiceBuilder. Apps anfitrionas con su propia orquestación de
     * persistencia (transacciones, alertas, manejo de archivos propio) deben
     * poner persist_stamp_result = false y persistir por su cuenta con el
     * PacStampResponse devuelto.
     */
    public function build(): PacStampResponse
    {
        $response = $this->pacProvider->stampInvoice();

        if ($response->getCheckProcess() && config('jiagbrody-laravel-factura-mx.persist_stamp_result', true)) {
            (new UpdateRecordsWhenStampingRevenueInvoiceAction)(
                invoice: $this->invoice,
                uuid: $response->getUuid(),
                xml: $response->getXml(),
            );
        }

        return $response;
    }
}
