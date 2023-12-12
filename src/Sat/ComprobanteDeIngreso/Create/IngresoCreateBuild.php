<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Create;

use CfdiUtils\CfdiCreator40;
use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDetail;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Helper\DraftBuild;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PdfFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Helper\XmlFileSatHelperBuilder;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use PhpCfdi\Credentials\Credential;

class IngresoCreateBuild extends DraftBuild
{
    protected Invoice $invoice;

    private bool $is_paid;
    private int  $payment_invoice_type_id;

    public function __construct(protected Credential $credential, protected CfdiCreator40 $creatorCfdi, protected InvoiceCompanyHelper $companyHelper, protected AttributeAssembly $attributeAssembly)
    {
    }

    public function saveInvoice(string $relationshipModel, int $relationshipId): Invoice
    {
        $invoice                     = new Invoice;
        $invoice->user_id            = auth()->id();
        $invoice->invoice_type_id    = InvoiceTypeEnum::INGRESO->value;
        $invoice->invoice_company_id = $this->companyHelper->id;
        $invoice->invoice_status_id  = InvoiceStatusEnum::DRAFT->value;
        $invoice->invoiceable_type   = $relationshipModel;
        $invoice->invoiceable_id     = $relationshipId;

        $invoice->save();

        $this->saveInvoiceDetails($invoice);

        return $invoice;
    }

    public function setInvoiceBalance(bool $isPaid, PaymentInvoiceTypeEnum $paymentInvoiceTypeId): self
    {
        $this->is_paid                 = $isPaid;
        $this->payment_invoice_type_id = $paymentInvoiceTypeId->value;

        return $this;
    }

    public function saveDraft(Collection $products): void
    {
        # GUARDO DETALLES DE LA FACTURA.
        $this->saveDraftDetails();

        # GUARDO VALORES DEL SISTEMA.
        $this->invoice->refresh();

        $balance = GetBalanceProductsHelper::make($products);

        $this->saveBalance($balance->charges);

        # GUARDO PRODUCTOS RELACIONADOS A LA FACTURA.
        $collection = $balance->products->keyBy('statement_detail_id')->map(function ($item) {
            $collect = collect();
            $collect->put('unit_price', $item->price_unit);
            $collect->put('gross_sub_total', $item->gross_sub_total);
            $collect->put('discount', $item->discount);
            $collect->put('sub_total', $item->sub_total);
            $collect->put('tax', $item->tax);
            $collect->put('total', $item->total);
            return $collect->toArray();
        });

        $this->invoice->statementDetails()->sync($collection->toArray());

        # GUARDO LOS IMPUESTOS DECLARADOS EN LA FACTURA DE INGRESO.
        $this->saveInvoiceTax();

        # GENERO DOCUMENTOS XML Y EL PDF.
        $xml = (new XmlFileSatHelperBuilder($this->invoice))->generate($this->creatorCfdi->asXml());
        (new PdfFileSatHelperBuilder())
            ->setInvoiceCfdiType($this->invoice->invoice_cfdi_type_id)
            ->setXmlContent($this->creatorCfdi->asXml())
            ->setXmlDocument($xml)
            ->build();
    }

    private function saveBalance($charges): void
    {
        $balance = $this->invoice->invoiceBalance()->firstOrNew();

        $balance->gross_sub_total         = $charges->gross_sub_total;
        $balance->sub_total               = $charges->sub_total;
        $balance->discount                = $charges->discount;
        $balance->tax                     = $charges->tax;
        $balance->total                   = $charges->total;
        $balance->local_tax               = $charges->local_tax;
        $balance->balance_total           = $charges->balance_total;
        $balance->is_paid                 = $this->is_paid;
        $balance->payment_invoice_type_id = $this->payment_invoice_type_id;
        $balance->save();
    }

    private function saveInvoiceTax(): void
    {
        $impuestos = $this->creatorCfdi->comprobante()->getImpuestos();
        // $impuestos = ConvertXmlContentToObjectHelper::make($this->creatorCfdi->asXml());
        // dd(!empty($impuestos->Impuestos), $impuestos->Impuestos);

        if ($impuestos->attributes()->count()) {
            $invoiceTaxDetails = [];

            if ($impuestos->attributes()->exists('TotalImpuestosTrasladados')) {
                $totalTraslados = $this->creatorCfdi->comprobante()->getImpuestos()->attributes()->get('TotalImpuestosTrasladados');

                foreach ($impuestos->getTraslados()->children() as $value) {
                    $invoiceTaxDetails[] = $this->addTaxDetail(InvoiceTaxTypeEnum::TRASLADO, $value->attributes());
                }
            }

            if ($impuestos->attributes()->exists('TotalImpuestosRetenidos')) {
                $totalRetenidos = $this->creatorCfdi->comprobante()->getImpuestos()->attributes()->get('TotalImpuestosRetenidos');

                foreach ($impuestos->getRetenciones()->children() as $value) {
                    $invoiceTaxDetails[] = $this->addTaxDetail(InvoiceTaxTypeEnum::RETENCION, $value->attributes());
                }
            }

            $invoiceTax                              = InvoiceTax::whereInvoiceId($this->invoice->id)->firstOrNew();
            $invoiceTax->invoice_id                  = $this->invoice->id;
            $invoiceTax->total_impuestos_trasladados = $totalTraslados ?? null;
            $invoiceTax->total_impuestos_retenidos   = $totalRetenidos ?? null;
            $invoiceTax->save();

            if (!empty($invoiceTaxDetails)) {
                $invoiceTax->invoiceTaxDetails()->delete();
                $invoiceTax->invoiceTaxDetails()->saveMany($invoiceTaxDetails);
            }
        }
    }

    private function addTaxDetail(InvoiceTaxTypeEnum $type, $attributes): InvoiceTaxDetails
    {
        $collect = collect([
            'invoice_tax_type_id' => $type->value,
            'base'                => $attributes->get('Base'),
            'impuesto'            => $attributes->get('Impuesto'),
            'tipo_factor'         => $attributes->get('TipoFactor'),
        ]);

        if ($attributes->get('TasaOCuota')) {
            $collect->put('tasa_o_cuota', $attributes->get('TasaOCuota'));
        }

        if ($attributes->get('Importe')) {
            $collect->put('importe', $attributes->get('Importe'));
        }

        return new InvoiceTaxDetails($collect->toArray());
    }

    private function saveInvoiceDetails(Invoice $invoice)
    {
        $details = new InvoiceDetail;

    }
}
