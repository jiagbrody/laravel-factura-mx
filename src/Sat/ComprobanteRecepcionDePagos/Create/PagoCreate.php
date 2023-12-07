<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\Create;

use App\Enums\InvoiceCompanyEnum;
use JiagBrody\LaravelFacturaMx\Sat\DraftSatInterface;
use Illuminate\Database\Eloquent\Collection;

readonly class PagoCreate implements DraftSatInterface
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
