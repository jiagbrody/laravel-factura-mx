<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

class ConvertNumberToReadableTextHelper
{
    public function __invoke(string $amount, string $currencyLabel, string $separatorLabel, string $decimalLabel): string
    {
        $num_word = '';
        $arr = explode('.', $amount);
        $entero = (int) $arr[0];
        if (isset($arr[1])) {
            $decimos = (int) (strlen($arr[1]) == 1 ? $arr[1].'0' : $arr[1]);
        }

        $fmt = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        if (is_array($arr)) {
            // $num_word = (($arr[0]) >= 1000000) ? "{$fmt->format($entero)} $currencyLabel" : "{$fmt->format($entero)} $currencyLabel";
            $num_word = "{$fmt->format($entero)} $currencyLabel";
            if (isset($decimos) && $decimos > 0) {
                $num_word .= " $separatorLabel {$fmt->format($decimos)} $decimalLabel";
            }
        }

        return ucfirst($num_word);
    }
}
