<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos;

use App\Enums\InvoiceCompanyEnum;
use App\Helpers\GetTruncateValueFromFloatTypeNumberHelper;
use App\Services\CurrencyExchangeRateService;
use CfdiUtils\Certificado\Certificado;
use CfdiUtils\Elements\Pagos20\Pagos;
use CfdiUtils\SumasPagos20\Calculator;
use CfdiUtils\SumasPagos20\Currencies;
use CfdiUtils\SumasPagos20\PagosWriter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;

class PagoCreateConcrete extends CfdiHelperAbstract
{
    private ComprobanteAtributos $atributos;

    protected CurrencyExchangeRateService $currencyService;

    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
        $this->atributos = new ComprobanteAtributos('P');
        $this->currencyService = new CurrencyExchangeRateService;
        parent::__construct($invoiceCompanyEnum);
    }

    public function addAtributos(array $atributos): self
    {
        //$this->atributos->Serie = $atributos['Serie'] ?? '';
        //$this->atributos->Folio = $atributos['Folio'] ?? '';
        //$this->atributos->Sello = '';
        //$this->atributos->NoCertificado = '';
        //$this->atributos->Certificado = '';
        //$this->atributos->SubTotal = '0';
        //$this->atributos->Moneda = 'XXX';
        //$this->atributos->Total = '0';
        //$this->atributos->Exportacion = '01';

        $this->creatorCfdi->comprobante()->addAttributes((array) $this->atributos);

        return $this;
    }

    public function addConceptos($products = new Collection()): self
    {
        $this->conceptoAtributos->ClaveProdServ = '84111506';
        $this->conceptoAtributos->Cantidad = '1';
        $this->conceptoAtributos->ClaveUnidad = 'ACT';
        $this->conceptoAtributos->Descripcion = 'Pago';
        $this->conceptoAtributos->ValorUnitario = '0';
        $this->conceptoAtributos->Importe = '0';
        $this->conceptoAtributos->ObjetoImp = '01';

        $this->creatorCfdi->comprobante()->addConcepto((array) $this->conceptoAtributos);

        return $this;
    }

    public function addComplemento(array $complementoRep = []): self
    {
        // $complemento = $this->calculateWithCfdiUitls($complementoRep); // TEST UTILIDAD DE PAGO CFDIUTILS
        $complemento = $this->calculateComplemento($complementoRep);

        $this->creatorCfdi->comprobante()->addComplemento($complemento);

        return $this;
    }

    public function build(): PagoCreateBuild
    {
        $this->creatorCfdi->putCertificado(new Certificado($this->credential->certificate()->pem()), false);
        $this->creatorCfdi->addSumasConceptos(null, 0);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();

        return new PagoCreateBuild($this->credential, $this->creatorCfdi, $this->companyHelper);
    }

    private function calculateComplemento($complemento): Pagos
    {
        $complementoPagos = new Pagos();
        $impuestosP = [];
        foreach ($complemento['Pagos']['Pago'] as $keyPago => $pago) {

            $propertiesPay = (new DefinePagoProperties())->setDinamicPropertiesValues($pago);

            $pay = $complementoPagos->addPago((array) $propertiesPay);

            $doctoRelacionados = collect($pago['DoctoRelacionado']);

            $invoices = Invoice::query()
                ->with(['cfdiActivePaid', 'invoiceDetail', 'invoiceTax.invoiceTaxDetails'])
                ->withCount([
                    'cfdiActivePaid' => function ($query) {
                        $query->where('is_active', true);
                    },
                ])
                ->whereHas('cfdi', function (Builder $query) use ($doctoRelacionados) {
                    $query->whereIn('uuid', $doctoRelacionados->pluck('IdDocumento'));
                })
                ->get();

            foreach ($doctoRelacionados as $doctoRelacionado) {
                $invoice = $invoices->where('cfdi.uuid', $doctoRelacionado['IdDocumento'])->firstOrFail();
                $documentR = ((new DefineDoctoRelacionadoProperties())->setDinamicPropertiesValues($propertiesPay, $invoice, $doctoRelacionado));
                $trasladosDR = GetImpuestosDRHelper::traslados($invoice, $documentR);
                $retencionesDR = (new DefineImpuestosDRProperties)->getImpuestosDRRetenciones($invoice, $documentR);

                $docrelacionado = $pay->addDoctoRelacionado((array) $documentR);

                if ($trasladosDR->isNotEmpty()) {
                    foreach ($trasladosDR as $item) {
                        $id = $item->ImpuestoDR.'_'.$item->TipoFactorDR;

                        if ($item->TipoFactorDR !== 'Exento') {
                            $id = $item->ImpuestoDR.'_'.$item->TipoFactorDR.'_'.$item->TasaOCuotaDR;
                            $impuestosP[$keyPago]['traslados'][$id]['TasaOCuotaP'] = $item->TasaOCuotaDR;

                            $importeP = $this->checkIfDifferentCurrencyToTruncate($item->ImporteDR, $propertiesPay, $documentR);
                            $impuestosP[$keyPago]['traslados'][$id]['ImporteP'] = $importeP + @$impuestosP[$keyPago]['traslados'][$id]['ImporteP'];
                        }

                        $baseP = $this->checkIfDifferentCurrencyToTruncate($item->BaseDR, $propertiesPay, $documentR);
                        $impuestosP[$keyPago]['traslados'][$id]['BaseP'] = $baseP + @$impuestosP[$keyPago]['traslados'][$id]['BaseP'];
                        $impuestosP[$keyPago]['traslados'][$id]['ImpuestoP'] = $item->ImpuestoDR;
                        $impuestosP[$keyPago]['traslados'][$id]['TipoFactorP'] = $item->TipoFactorDR;

                        $docrelacionado->addImpuestosDR()->addTrasladosDR()->addTrasladoDR((array) $item);
                    }
                }

                if ($retencionesDR->isNotEmpty()) {
                    foreach ($retencionesDR as $item) {
                        $id = $item->ImpuestoDR;
                        $impuestosP[$keyPago]['retenciones'][$id]['ImpuestoP'] = $item->ImpuestoDR;

                        $importeP = $this->checkIfDifferentCurrencyToTruncate($item->ImporteDR, $propertiesPay, $documentR);
                        $impuestosP[$keyPago]['retenciones'][$id]['ImporteP'] = $importeP + @$impuestosP[$keyPago]['retenciones'][$id]['ImporteP'];

                        $docrelacionado->addImpuestosDR()->addRetencionesDR()->addRetencionDR((array) $item);
                    }
                }
            } // ENDFOREACH DOCUMENT RELATED

            if (isset($impuestosP[$keyPago]['traslados'])) {
                foreach ($impuestosP[$keyPago]['traslados'] as $impPTraslado) {
                    $pay->addImpuestosP()->addTrasladosP()->addTrasladoP($impPTraslado);
                }
            }

            if (isset($impuestosP[$keyPago]['retenciones'])) {
                foreach ($impuestosP[$keyPago]['retenciones'] as $impPRetencion) {
                    $pay->addImpuestosP()->addRetencionesP()->addRetencionP($impPRetencion);
                }
            }
        } // ENDFOREACH PAY

        $totales = [];
        foreach ($impuestosP as $payKey => $taxValue) {
            $tipoCambioP = $complemento['Pagos']['Pago'][$payKey]['TipoCambioP'];
            if (isset($taxValue['traslados'])) {
                foreach ($taxValue['traslados'] as $key => $tax) {
                    switch ($key) {
                        case '002_Tasa_0.160000':
                            $totales['TotalTrasladosBaseIVA16'] = ($tax['BaseP'] * $tipoCambioP) + @$totales['TotalTrasladosBaseIVA16'];
                            $totales['TotalTrasladosImpuestoIVA16'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalTrasladosImpuestoIVA16'];
                            break;
                        case '002_Tasa_0.080000':
                            $totales['TotalTrasladosBaseIVA8'] = ($tax['BaseP'] * $tipoCambioP) + @$totales['TotalTrasladosBaseIVA8'];
                            $totales['TotalTrasladosImpuestoIVA8'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalTrasladosImpuestoIVA8'];
                            break;
                        case '002_Tasa_0.000000':
                            $totales['TotalTrasladosBaseIVA0'] = ($tax['BaseP'] * $tipoCambioP) + @$totales['TotalTrasladosBaseIVA0'];
                            $totales['TotalTrasladosImpuestoIVA0'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalTrasladosImpuestoIVA0'];
                            break;
                        case '002_Exento':
                            $totales['TotalTrasladosBaseIVAExento'] = ($tax['BaseP'] * $tipoCambioP) + @$totales['TotalTrasladosBaseIVAExento'];
                            break;
                    }
                }
            }

            if (isset($taxValue['retenciones'])) {
                foreach ($taxValue['retenciones'] as $key => $tax) {
                    switch ($key) {
                        case '001':
                            $totales['TotalRetencionesISR'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalRetencionesISR'];
                            break;
                        case '002':
                            $totales['TotalRetencionesIVA'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalRetencionesIVA'];
                            break;
                        case '003':
                            $totales['TotalRetencionesIEPS'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalRetencionesIEPS'];
                            break;
                    }
                }
            }

            $monto = $complemento['Pagos']['Pago'][$payKey]['Monto'];
            $totales['MontoTotalPagos'] = ($monto * $tipoCambioP) + @$totales['MontoTotalPagos'];
        }

        // REDONDEAR A 2 DECIMALES
        foreach ($totales as $key => $total) {
            $totales[$key] = round($total, 2);
        }

        $complementoPagos->addTotales($totales);

        return $complementoPagos;
    }

    private function checkIfDifferentCurrencyToTruncate($value, DefinePagoProperties $pay, DefineDoctoRelacionadoProperties $docto)
    {
        $importeP = $value / $docto->EquivalenciaDR;
        if ($pay->MonedaP !== $docto->MonedaDR) {
            $importeP = GetTruncateValueFromFloatTypeNumberHelper::make($importeP, 6);
        }

        return $importeP;
    }

    private function calculateWithCfdiUitls(array $complemento)
    {

        $complementoPagos = new Pagos();
        $impuestosP = [];
        foreach ($complemento['Pagos']['Pago'] as $keyPago => $pago) {

            $propertiesPay = (new DefinePagoProperties())->setDinamicPropertiesValues($pago);

            $pay = $complementoPagos->addPago((array) $propertiesPay);

            $doctoRelacionados = collect($pago['DoctoRelacionado']);

            $invoices = Invoice::query()
                ->with(['cfdiActivePaid', 'invoiceDetail', 'invoiceTax.invoiceTaxDetails'])
                ->withCount([
                    'cfdiActivePaid' => function ($query) {
                        $query->where('is_active', true);
                    },
                ])
                ->whereHas('cfdi', function (Builder $query) use ($doctoRelacionados) {
                    $query->whereIn('uuid', $doctoRelacionados->pluck('IdDocumento'));
                })
                ->get();

            foreach ($doctoRelacionados as $doctoRelacionado) {
                $invoice = $invoices->where('cfdi.uuid', $doctoRelacionado['IdDocumento'])->firstOrFail();
                $documentR = ((new DefineDoctoRelacionadoProperties())->setDinamicPropertiesValues($propertiesPay, $invoice, $doctoRelacionado));
                $trasladosDR = GetImpuestosDRHelper::traslados($invoice, $documentR);
                $retencionesDR = (new DefineImpuestosDRProperties)->getImpuestosDRRetenciones($invoice, $documentR);

                $docrelacionado = $pay->addDoctoRelacionado((array) $documentR);

                if ($trasladosDR->isNotEmpty()) {
                    foreach ($trasladosDR as $item) {
                        $id = $item->ImpuestoDR.'_'.$item->TipoFactorDR;

                        if ($item->TipoFactorDR !== 'Exento') {
                            $id = $item->ImpuestoDR.'_'.$item->TipoFactorDR.'_'.$item->TasaOCuotaDR;
                            $impuestosP[$keyPago]['traslados'][$id]['TasaOCuotaP'] = $item->TasaOCuotaDR;

                            $importeP = $this->checkIfDifferentCurrencyToTruncate($item->ImporteDR, $propertiesPay, $documentR);
                            $impuestosP[$keyPago]['traslados'][$id]['ImporteP'] = $importeP + @$impuestosP[$keyPago]['traslados'][$id]['ImporteP'];
                        }

                        $baseP = $this->checkIfDifferentCurrencyToTruncate($item->BaseDR, $propertiesPay, $documentR);
                        $impuestosP[$keyPago]['traslados'][$id]['BaseP'] = $baseP + @$impuestosP[$keyPago]['traslados'][$id]['BaseP'];
                        $impuestosP[$keyPago]['traslados'][$id]['ImpuestoP'] = $item->ImpuestoDR;
                        $impuestosP[$keyPago]['traslados'][$id]['TipoFactorP'] = $item->TipoFactorDR;

                        $docrelacionado->addImpuestosDR()->addTrasladosDR()->addTrasladoDR((array) $item);
                    }
                }

                if ($retencionesDR->isNotEmpty()) {
                    foreach ($retencionesDR as $item) {
                        $id = $item->ImpuestoDR;
                        $impuestosP[$keyPago]['retenciones'][$id]['ImpuestoP'] = $item->ImpuestoDR;

                        $importeP = $this->checkIfDifferentCurrencyToTruncate($item->ImporteDR, $propertiesPay, $documentR);
                        $impuestosP[$keyPago]['retenciones'][$id]['ImporteP'] = $importeP + @$impuestosP[$keyPago]['retenciones'][$id]['ImporteP'];

                        $docrelacionado->addImpuestosDR()->addRetencionesDR()->addRetencionDR((array) $item);
                    }
                }
            } // ENDFOREACH DOCUMENT RELATED

            if (isset($impuestosP[$keyPago]['traslados'])) {
                foreach ($impuestosP[$keyPago]['traslados'] as $impPTraslado) {
                    $pay->addImpuestosP()->addTrasladosP()->addTrasladoP($impPTraslado);
                }
            }

            if (isset($impuestosP[$keyPago]['retenciones'])) {
                foreach ($impuestosP[$keyPago]['retenciones'] as $impPRetencion) {
                    $pay->addImpuestosP()->addRetencionesP()->addRetencionP($impPRetencion);
                }
            }
        } // ENDFOREACH PAY

        $totales = [];
        foreach ($impuestosP as $payKey => $taxValue) {
            $tipoCambioP = $complemento['Pagos']['Pago'][$payKey]['TipoCambioP'];
            if (isset($taxValue['traslados'])) {
                foreach ($taxValue['traslados'] as $key => $tax) {
                    switch ($key) {
                        case '002_Tasa_0.160000':
                            $totales['TotalTrasladosBaseIVA16'] = ($tax['BaseP'] * $tipoCambioP) + @$totales['TotalTrasladosBaseIVA16'];
                            $totales['TotalTrasladosImpuestoIVA16'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalTrasladosImpuestoIVA16'];
                            break;
                        case '002_Tasa_0.080000':
                            $totales['TotalTrasladosBaseIVA8'] = ($tax['BaseP'] * $tipoCambioP) + @$totales['TotalTrasladosBaseIVA8'];
                            $totales['TotalTrasladosImpuestoIVA8'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalTrasladosImpuestoIVA8'];
                            break;
                        case '002_Tasa_0.000000':
                            $totales['TotalTrasladosBaseIVA0'] = ($tax['BaseP'] * $tipoCambioP) + @$totales['TotalTrasladosBaseIVA0'];
                            $totales['TotalTrasladosImpuestoIVA0'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalTrasladosImpuestoIVA0'];
                            break;
                        case '002_Exento':
                            $totales['TotalTrasladosBaseIVAExento'] = ($tax['BaseP'] * $tipoCambioP) + @$totales['TotalTrasladosBaseIVAExento'];
                            break;
                    }
                }
            }

            if (isset($taxValue['retenciones'])) {
                foreach ($taxValue['retenciones'] as $key => $tax) {
                    switch ($key) {
                        case '001':
                            $totales['TotalRetencionesISR'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalRetencionesISR'];
                            break;
                        case '002':
                            $totales['TotalRetencionesIVA'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalRetencionesIVA'];
                            break;
                        case '003':
                            $totales['TotalRetencionesIEPS'] = ($tax['ImporteP'] * $tipoCambioP) + @$totales['TotalRetencionesIEPS'];
                            break;
                    }
                }
            }

            $monto = $complemento['Pagos']['Pago'][$payKey]['Monto'];
            $totales['MontoTotalPagos'] = ($monto * $tipoCambioP) + @$totales['MontoTotalPagos'];
        }

        // REDONDEAR A 2 DECIMALES
        foreach ($totales as $key => $total) {
            $totales[$key] = round($total, 2);
        }

        // dd($complementoPagos);

        // Se puede usar el método estático
        // dd($complementoPagos->);
        PagosWriter::calculateAndPut($complementoPagos);

        // Se puede calcular y mandar a escribir
        $pagosCalculator = new Calculator(
            2, // Decimales a usar en los impuestos de los pagos
            new Currencies(['MXN' => 2, 'USD' => '2', 'EUR' => 2]) // Monedas con decimales
        );
        $result = $pagosCalculator->calculate($complementoPagos);
        $pagosWriter = new PagosWriter($complementoPagos);
        $pagosWriter->writePago();

    }
}
