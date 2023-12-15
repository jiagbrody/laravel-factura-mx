<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use CfdiUtils\CfdiCreator40;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use PhpCfdi\Credentials\Credential;

abstract class DraftBuild implements DraftBuildInterface
{
    /*protected CfdiCreator40 $creatorCfdi;
    protected Credential    $credential;
    protected Invoice       $invoice;

    public function setInvoice(Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function saveInvoice(string $relationshipModel, int $relationshipId)
    {
    }

    public function getObjectFromComprobanteData(): \stdClass
    {
        return ConvertXmlContentToObjectHelper::make($this->creatorCfdi->asXml());
    }

    protected function saveDraftDetails(): void
    {
        $data     = $this->getObjectFromComprobanteData();
        $emisor   = $data->Emisor;
        $receptor = $data->Receptor;

        $invoiceDetail                      = InvoiceDetail::whereInvoiceId($this->invoice->id)->firstOrNew();
        $invoiceDetail->invoice_id          = $this->invoice->id;
        $invoiceDetail->version             = $data->Version;
        $invoiceDetail->serie               = $data->Serie;
        $invoiceDetail->folio               = (string)$this->invoice->id;
        $invoiceDetail->fecha               = $data->Fecha;
        $invoiceDetail->forma_pago          = $data->FormaPago ?? null;
        $invoiceDetail->condiciones_de_pago = $data->CondicionesDePago ?? null;
        $invoiceDetail->tipo_de_comprobante = $data->TipoDeComprobante;
        $invoiceDetail->metodo_pago         = $data->MetodoPago ?? null;
        $invoiceDetail->exportacion         = $data->Exportacion ?? null;
        $invoiceDetail->lugar_expedicion    = $data->LugarExpedicion;
        $invoiceDetail->moneda              = $data->Moneda;
        $invoiceDetail->tipo_cambio         = $data->TipoCambio ?? null;
        $invoiceDetail->descuento           = $data->Descuento ?? null;
        $invoiceDetail->sub_total           = $data->SubTotal;
        $invoiceDetail->total               = $data->Total;
        $invoiceDetail->emisor_rfc          = $emisor->Rfc;
        $invoiceDetail->receptor_rfc        = $receptor->Rfc;
        $invoiceDetail->save();

        $this->SaveRelatedCfdis($data->CfdiRelacionados ?? []);

        # MODIFICO EL FOLIO
        $this->creatorCfdi->comprobante()->addAttributes(['Folio' => $invoiceDetail->folio]);
        # SELLO EL DOCUMENTO PARA RESTRINGIR CUALQUIER MODIFICACION EN EL COMPROBANTE
        $this->creatorCfdi->addSello($this->credential->privateKey()->pem(), $this->credential->privateKey()->passPhrase());
    }

    private function SaveRelatedCfdis(array $relacionados): void
    {
        if ($relacionados) {
            $this->invoice->relatedCfdis()->detach();
            foreach ($relacionados as $related) {
                $enum  = CfdiRelationTypeEnum::from($related->TipoRelacion);
                $uuids = collect($related->CfdiRelacionado)->pluck('UUID');
                $cfdis = Cfdi::whereIn('uuid', $uuids)->get()->pluck('id');
                $maped = $cfdis->mapWithKeys(function ($item) use ($enum) {
                    return [$item => ['related_invoice_id' => $enum->id()]];
                });

                $this->invoice->relatedCfdis()->attach($maped);
            }
        }
    }*/
}
