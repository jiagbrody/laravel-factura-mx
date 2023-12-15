<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos;

use Carbon\Carbon;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\PatronDeDatosHelper;

class DefinePagoProperties
{
    use HelperConstantsTrait;

    public string $FechaPago;

    public string $FormaDePagoP;

    public string $MonedaP;

    public string $TipoCambioP;

    public string $Monto;

    public string $NumOperacion;

    public string $RfcEmisorCtaOrd;

    public string $NomBancoOrdExt;

    public string $CtaOrdenante;

    public string $RfcEmisorCtaBen;

    public string $CtaBeneficiario;

    public string $TipoCadPago;

    public string $CertPago;

    public string $CadPago;

    public string $SelloPago;

    public function setDinamicPropertiesValues(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (($key === $this->KEY_TIPO_CAMBIO()) || (! empty($value) && property_exists($this, $key))) {
                if ($key === $this->KEY_TIPO_CAMBIO()) {
                    if ($attributes[$this->KEY_MONEDA()] === $this->VALUE_MONEDA_MXN()) {
                        $this->TipoCambioP = '1';
                    } else {
                        $this->$key = (string) $value;
                    }
                } elseif ($key === 'FechaPago') {
                    $this->FechaPago = PatronDeDatosHelper::t_FechaH(Carbon::createFromFormat('Y-m-d\TH:i:s', $this->getCorrectDateTimeFromInputDateTime($attributes[$this->KEY_FECHA_PAGO()])));
                } else {
                    $this->$key = (string) $value;
                }
            }
        }

        return $this;
    }

    private function getCorrectDateTimeFromInputDateTime(string $dateTime): string
    {
        $date = explode('T', $dateTime);
        $time = explode(':', $date[1]);

        return array_key_exists(2, $time) ? $dateTime : $dateTime.':00';
    }
}
