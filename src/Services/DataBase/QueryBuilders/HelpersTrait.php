<?php

namespace JiagBrody\LaravelFacturaMx\Services\DataBase\QueryBuilders;

trait HelpersTrait
{
    protected function checkLogicalErrorTrait(): void
    {
        if (! property_exists($this, 'invoice')) {
            abort(422, 'NO ESTA DEFINIDA LA PROPIEDAD "invoice" en la clase "DatabaseService"');
        }

        if ($this->invoice->exists === false) {
            abort(422, 'ES NECESARIO DECLARAR UN MODELO "Invoice" en la clase "DatabaseService"');
        }
    }
}
