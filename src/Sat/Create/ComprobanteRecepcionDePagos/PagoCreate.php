<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos;

use App\Enums\InvoiceCompanyEnum;
use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Sat\GettingSatCfdiInterface;

readonly class PagoCreate implements GettingSatCfdiInterface
{
    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
    }

    public function custom($company): PagoCreateConcrete
    {
        return new PagoCreateConcrete($this->invoiceCompanyEnum);
    }

    public function fromComprobante(array $comprobante, ?Collection $products = null): PagoCreateConcrete
    {
        return (new PagoCreateConcrete($this->invoiceCompanyEnum))->addAtributos($comprobante)
            ->addReceptor($comprobante['Receptor'])
            ->addConceptos()
            ->addRelacionados($comprobante['CfdiRelacionados'])
            ->addComplemento($comprobante['Complemento']);
    }
}
