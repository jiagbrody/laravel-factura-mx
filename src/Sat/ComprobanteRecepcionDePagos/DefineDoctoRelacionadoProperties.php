<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos;

use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\PatronDeDatosHelper;

class DefineDoctoRelacionadoProperties
{
    use HelperConstantsTrait;

    public string $IdDocumento;

    public string $Serie;

    public string $Folio;

    public string $MonedaDR;

    public float $EquivalenciaDR;

    public int $NumParcialidad;

    public float $ImpSaldoAnt;

    public float $ImpPagado;

    public float $ImpSaldoInsoluto;

    public string $ObjetoImpDR;

    public function setDinamicPropertiesValues(DefinePagoProperties $propertiesPay, Invoice $invoice, $attributes): self
    {
        $detail = $invoice->invoiceDetail;
        $activePaid = $invoice->cfdiActivePaid->where('is_active', '=', true);

        $sumTotalActivePaid = $activePaid->sum('imp_pagado');
        $parcialidad = $invoice->cfdi_active_paid_count + 1;
        $pagoMonto = (float) $propertiesPay->Monto;
        $impPagado = (float) $attributes['ImpPagado'];

        $this->IdDocumento = $invoice->cfdi->uuid;
        $this->Serie = $invoice->invoiceDetail->serie;
        $this->Folio = $invoice->invoiceDetail->folio;
        $this->MonedaDR = (empty($detail->moneda) || $this->VALUE_MONEDA_MXN() === $detail->moneda) ? $this->VALUE_MONEDA_MXN() : $detail->moneda;

        $equivalenciaDR = round($impPagado / $pagoMonto, 10);
        $this->EquivalenciaDR = ($propertiesPay->MonedaP === $this->MonedaDR) ? 1 : $equivalenciaDR;

        $this->NumParcialidad = $parcialidad;
        $this->ImpSaldoAnt = PatronDeDatosHelper::t_import((float) $invoice->invoiceDetail->total - $sumTotalActivePaid);
        $this->ImpPagado = PatronDeDatosHelper::t_import($impPagado);
        $this->ImpSaldoInsoluto = (PatronDeDatosHelper::t_import($this->ImpSaldoAnt - $impPagado));
        $this->ObjetoImpDR = $this->DEFAULT_VALUE_OBJECT_IMP();

        return $this;
    }
}
