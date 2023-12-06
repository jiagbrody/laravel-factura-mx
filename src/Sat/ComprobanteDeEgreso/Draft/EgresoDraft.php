<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Draft;

use App\Enums\InvoiceCompanyEnum;
use JiagBrody\LaravelFacturaMx\Sat\DraftSatInterface;

readonly class EgresoDraft implements DraftSatInterface
{
    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
    }

    public function createCustom(): EgresoDraftConcrete
    {
        return new EgresoDraftConcrete($this->invoiceCompanyEnum);
    }

    public function createFillDataFromComprobanteFormData(array $comprobante, $products = []): EgresoDraftConcrete
    {
        $concept = (!empty($products)) ? $products : $comprobante['Conceptos']['Concepto'][0];

        return (new EgresoDraftConcrete($this->invoiceCompanyEnum))
            ->addAtributos($comprobante)
            ->addReceptor($comprobante['Receptor'])
            ->addConceptos($concept)
            ->addRelacionados($comprobante['CfdiRelacionados']);
    }
}
