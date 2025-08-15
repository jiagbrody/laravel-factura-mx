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
        if (self::validateNationalGenericRfc($rfc) || self::validateForeignGenericRfc($rfc)) {
            return true;
        }

        return false;
    }

    /**
     * Validates if the given RFC corresponds to the national generic RFC.
     *
     * @param  string  $rfc  The RFC string to be validated.
     * @return bool Returns true if the RFC matches the national generic RFC, otherwise returns false.
     */
    public function validateNationalGenericRfc(string $rfc): bool
    {
        return $rfc === CfdiGenericRfcEnum::NACIONAL->value;
    }

    /**
     * Validates if the given RFC corresponds to the foreign generic RFC.
     *
     * @param  string  $rfc  The RFC string to be validated.
     * @return bool Returns true if the RFC matches the foreign generic RFC, otherwise returns false.
     */
    public function validateForeignGenericRfc(string $rfc): bool
    {
        return $rfc === CfdiGenericRfcEnum::EXTRANJERO->value;
    }
}
