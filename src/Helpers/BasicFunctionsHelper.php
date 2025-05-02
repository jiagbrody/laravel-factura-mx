<?php

namespace JiagBrody\LaravelFacturaMx\Helpers;

use Carbon\Carbon;
use JiagBrody\LaravelFacturaMx\Enums\CfdiGenericRfcEnum;

class BasicFunctionsHelper
{
    public function changeDateIfItIsGreaterThanTheDeadline($date): string
    {
        $deadline = 72; // 72 horas permitidas para el SAT para facturas hechas y no timbradas.

        $xmlCarbonDate = Carbon::make($date);
        $deadlineCarbonDate = Carbon::make($date)->addHours($deadline);
        $currentCarbonDate = Carbon::make(now());

        if ($currentCarbonDate->gt($deadlineCarbonDate)) {
            $xmlCarbonDate = $currentCarbonDate;
        }

        return $xmlCarbonDate->format('Y-m-d\TH:i:s');
    }

    public function checkIfItIsAGenericRfc(string $rfc): bool
    {
        if ($rfc === CfdiGenericRfcEnum::NACIONAL->value || $rfc === CfdiGenericRfcEnum::EXTRANJERO->value) {
            return true;
        }

        return false;
    }
}
