<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Draft;

use App\Enums\InvoiceCompanyEnum;
use JiagBrody\LaravelFacturaMx\Sat\DraftSatInterface;
use Illuminate\Database\Eloquent\Collection;

readonly class IngresoDraft implements DraftSatInterface
{
    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
    }

    public function createCustom(): IngresoDraftConcrete
    {
        return new IngresoDraftConcrete($this->invoiceCompanyEnum);
    }

    public function createFillDataFromComprobanteFormData(array $comprobante, Collection $products): IngresoDraftConcrete
    {
        /*foreach ($comprobante as $key => $value) {
            if ($value === null) {
                unset($comprobante[$key]);
            }
        }*/

        return (new IngresoDraftConcrete($this->invoiceCompanyEnum))->addAtributos($comprobante)
            ->addReceptor($comprobante['Receptor'])
            ->addConceptos($products)
            ->addRelacionados($comprobante['CfdiRelacionados'])
            ->addComplementoImpuestosLocales(impuestosLocales: $comprobante['Complemento']['ImpuestosLocales'], total: $products->sum('total'));
    }
}
