<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use Illuminate\Database\Eloquent\Collection;

interface DraftSatInterface
{
    public function createCustom();

    public function createFillDataFromComprobanteFormData(array $comprobante, Collection $products);
}
