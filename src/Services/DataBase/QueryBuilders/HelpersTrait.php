<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders;

use JiagBrody\LaravelFacturaMx\Exceptions\FacturaMxException;

trait HelpersTrait
{
    protected function checkLogicalErrorTrait(): void
    {
        if (! property_exists($this, 'invoice')) {
            throw new FacturaMxException('No está definida la propiedad "invoice" en la clase "DatabaseService".');
        }

        if ($this->invoice->exists === false) {
            throw new FacturaMxException('Es necesario declarar un modelo "Invoice" existente en la clase "DatabaseService" (llama a setInvoice() con una factura persistida).');
        }
    }
}
