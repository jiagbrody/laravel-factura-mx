<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\Draft;

use App\Enums\InvoiceCompanyEnum;
use JiagBrody\LaravelFacturaMx\Sat\DraftSatInterface;
use Illuminate\Database\Eloquent\Collection;

readonly class PagoDraft implements DraftSatInterface
{
    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
    }

    public function createCustom(): PagoDraftConcrete
    {
        return new PagoDraftConcrete($this->invoiceCompanyEnum);
    }

    public function createFillDataFromComprobanteFormData(array $comprobante, ?Collection $products = null): PagoDraftConcrete
    {
        return (new PagoDraftConcrete($this->invoiceCompanyEnum))->addAtributos($comprobante)
            ->addReceptor($comprobante['Receptor'])
            ->addConceptos()
            ->addRelacionados($comprobante['CfdiRelacionados'])
            ->addComplemento($comprobante['Complemento']);
    }
}
