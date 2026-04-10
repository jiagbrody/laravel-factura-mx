<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos;

use CfdiUtils\Elements\Pagos20\Pago;
use CfdiUtils\Elements\Pagos20\Pagos;
use CfdiUtils\SumasPagos20\PagosWriter;
use JiagBrody\LaravelFacturaMx\Facades\LaravelFacturaMx;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\Create\Helpers\CreateBuild;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\PatronDeDatosHelper;

class PagoCreator extends CfdiHelperAbstract
{
    protected Pagos $complementoPagos;

    private const TIPO_FACTOR_EXENTO = 'Exento';

    public function __construct(InvoiceCompany $invoiceCompany)
    {
        $this->companyHelper = new InvoiceCompanyHelper($invoiceCompany);
        parent::__construct();
        $this->complementoPagos = new Pagos;
    }

    public function addPay(array $pay): Pago
    {
        return $this->complementoPagos->addPago($pay);
    }

    public function addDoctoRelacionadoToPay($pay, array $relatedDocument): void
    {
        // Agrego el documento al pago
        $docRelacionado = $pay->addDoctoRelacionado($relatedDocument);

        // Obtengo el CFDI para obtener el XML y obtenerlo en Json
        $cfdi = InvoiceCfdi::where('uuid', $relatedDocument['IdDocumento'])->first();
        $laravelFacturaMx = LaravelFacturaMx::documentService();
        $laravelFacturaMx->setInvoice($cfdi->invoice);
        $xmlObject = $laravelFacturaMx->getXmlObject();

        // Obtengo el porcentaje de que se está pagando de la deuda total.
        $payoutPercentage = (float) $docRelacionado->attributes()->get('ImpPagado') / (float) $xmlObject->Total;

        $collect = collect();
        foreach ($xmlObject->Impuestos->Traslados as $traslado) {
            foreach ($traslado as $trasladoItem) {
                $object = new DefineImpuestosDRProperties;

                // TODO: SACAR DE LO QUE QUEDE PENDIENTE.
                $baseDR = ($trasladoItem->Base * $payoutPercentage);

                $object->setBaseDR(PatronDeDatosHelper::t_import($baseDR));
                $object->setImpuestoDR($trasladoItem->Impuesto);
                $object->setTipoFactorDR($trasladoItem->TipoFactor);

                if ($object->getTipoFactorDR() !== self::TIPO_FACTOR_EXENTO) {
                    $object->setTasaOCuotaDR($trasladoItem->TasaOCuota);
                    $object->setImporteDR(PatronDeDatosHelper::t_import($baseDR * $object->getTasaOCuotaDR()));
                }

                $collect->push((array) $object);

                $TrasladoDR = $docRelacionado->addImpuestosDR()->addTrasladosDR()->addTrasladoDR(
                    (array) $object
                );
            }
        }
    }

    /*
     * GUARDO EL COMPLEMENTO DE PAGOS TAL CUAL ENVIADO POR NAVEGADOR, ES MUY COMPLEJO HACER CLASES PARA ESTRUCTURARLO.
     * TODO: Quedaria mejor hacerlo por clases propias para estructurarlo.
     */
    public function addComplementoRecepcionDePagos($formData): void
    {
        $this->attributeAssembly->setComplementoRecepcionDePagos($formData);
    }

    public function build(): CreateBuild
    {
        // add calculated values to pagos (totales, pagos montos y pagos impuestos)
        PagosWriter::calculateAndPut($this->complementoPagos);

        // Agrego el complemento a la factura.
        $this->creatorCfdi->comprobante()->addComplemento($this->complementoPagos);

        $this->creatorCfdi->addSumasConceptos(null, 0);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();
        $this->creatorCfdi->addSello($this->credential->privateKey()->pem(), $this->credential->privateKey()->passPhrase());

        // perform validations, it should not have any error nor warnings
        $findings = $this->creatorCfdi->validate();
        // if ($findings->hasErrors()) {
        //     dd($findings);
        // }

        return new CreateBuild(
            xmlContent: $this->creatorCfdi->asXml(),
            companyHelper: $this->companyHelper,
            attributeAssembly: $this->attributeAssembly
        );
    }
}
