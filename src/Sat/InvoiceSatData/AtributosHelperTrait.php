<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData;

trait AtributosHelperTrait
{
    /*
     * Para pasar los valores a la utilidad de la creación de la factura con la librería PHPCFDI
     */
    public function getCollection(): \Illuminate\Support\Collection
    {
        $collection = collect();
        foreach ($this as $key => $value) {
            $collection->put($key, $value);
        }

        return $collection;
    }
}
