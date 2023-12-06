<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use App\Enums\CfdiGenericRfcEnum;
use App\Enums\InvoiceCompanyEnum;
use App\Models\DataFiscal;
use App\Models\Product;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\EmisorAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\PatronDeDatosHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ReceptorAtributos;
use CfdiUtils\CfdiCreator40;
use Illuminate\Database\Eloquent\Collection;
use PhpCfdi\Credentials\Credential;

abstract class CfdiHelperAbstract
{
    protected Credential $credential;

    protected \CfdiUtils\CfdiCreator40 $creatorCfdi;

    protected InvoiceCompanyHelper $companyHelper;

    protected EmisorAtributos $emisorAtributos;

    protected ReceptorAtributos $receptorAtributos;

    protected ConceptoAtributos $conceptoAtributos;

    protected ImpuestoTrasladoAtributos $impuestoTrasladoAtributos;

    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
        $this->creatorCfdi               = new CfdiCreator40();
        $this->companyHelper             = new InvoiceCompanyHelper($invoiceCompanyEnum->value);
        $this->credential                = Credential::openFiles($this->companyHelper->pathCertificado,
            $this->companyHelper->pathKey, $this->companyHelper->passPhrase);
        $this->emisorAtributos           = new EmisorAtributos;
        $this->conceptoAtributos         = new ConceptoAtributos;
        $this->receptorAtributos         = new ReceptorAtributos;
        $this->impuestoTrasladoAtributos = new ImpuestoTrasladoAtributos;

        $this->setEmisor();
    }

    public function addRelacionados(array $relacionados): static
    {
        foreach ($relacionados as $relacionado) {
            $this->creatorCfdi->comprobante()->addCfdiRelacionados([
                'TipoRelacion' => $relacionado['TipoRelacion'],
            ])->multiCfdiRelacionado(...$relacionado['CfdiRelacionado']);
        }

        return $this;
    }

    private function setEmisor(): void
    {
        $this->emisorAtributos->Nombre        = $this->companyHelper->nombre;
        $this->emisorAtributos->Rfc           = $this->companyHelper->rfc;
        $this->emisorAtributos->RegimenFiscal = $this->companyHelper->regimenFiscal;

        $this->creatorCfdi->comprobante()->addEmisor((array)$this->emisorAtributos);
    }

    public function addReceptor(DataFiscal|array $fiscalData): self
    {
        if (is_array($fiscalData)) {
            $this->receptorAtributos->Rfc                     = $fiscalData['Rfc'] ?? '';
            $this->receptorAtributos->Nombre                  = $fiscalData['Nombre'] ?? '';
            $this->receptorAtributos->DomicilioFiscalReceptor = $fiscalData['DomicilioFiscalReceptor'] ?? '';
            $this->receptorAtributos->ResidenciaFiscal        = $fiscalData['ResidenciaFiscal'] ?? '';
            $this->receptorAtributos->NumRegIdTrib            = $fiscalData['NumRegIdTrib'] ?? '';
            $this->receptorAtributos->RegimenFiscalReceptor   = $fiscalData['RegimenFiscalReceptor'] ?? '';
            $this->receptorAtributos->UsoCFDI                 = $fiscalData['UsoCFDI'] ?? '';
        } else {
            $this->receptorAtributos->Rfc                     = (string)$fiscalData->rfc;
            $this->receptorAtributos->Nombre                  = (string)$fiscalData->nombre;
            $this->receptorAtributos->DomicilioFiscalReceptor = (string)$fiscalData->domicilio_fiscal_receptor;
            $this->receptorAtributos->ResidenciaFiscal        = (string)$fiscalData->residencia_fiscal;
            $this->receptorAtributos->NumRegIdTrib            = (string)$fiscalData->num_reg_id_trib;
            $this->receptorAtributos->RegimenFiscalReceptor   = (string)$fiscalData->regimen_fiscal_receptor;
            $this->receptorAtributos->UsoCFDI                 = (string)$fiscalData->uso_cfdi;
        }

        if ($this->receptorAtributos->NumRegIdTrib === '') {
            unset($this->receptorAtributos->NumRegIdTrib);
            unset($this->receptorAtributos->ResidenciaFiscal);
        }

        // DOMICILIO FISCAL RECEPTOR PARA EL RECEPTOR OBLIGATORIO EN RFC GENERICOS
        switch ($this->receptorAtributos->Rfc) {
            case CfdiGenericRfcEnum::NACIONAL->value:
            case CfdiGenericRfcEnum::EXTRANJERO->value:
                $this->receptorAtributos->DomicilioFiscalReceptor = $this->atributos->LugarExpedicion;
        }

        $this->creatorCfdi->comprobante()->addReceptor((array)$this->receptorAtributos);

        return $this;
    }

    public function addConceptos(Collection $products): self
    {
        if ($products->count() > 0) {
            $products->each(function ($item) {
                $this->creatorCfdi->comprobante()
                    ->addConcepto((array)$this->getConcept($item))
                    ->addTraslado((array)$this->getTraslado($item));
            });
        }

        return $this;
    }

    private function getConcept(Product $product): \App\Services\SAT\InvoiceSatData\ConceptoAtributos
    {
        $this->conceptoAtributos->ClaveProdServ    = $product->c_clave_prod_serv;
        $this->conceptoAtributos->NoIdentificacion = (string)$product->statement_detail_id;
        $this->conceptoAtributos->Cantidad         = (string)$product->quantity;
        $this->conceptoAtributos->ClaveUnidad      = $product->c_clave_unidad;
        $this->conceptoAtributos->Descripcion      = ($product->comments) ? $product->name . ' - ' . $product->comments : $product->name;
        $this->conceptoAtributos->ValorUnitario    = (string)PatronDeDatosHelper::t_import($product->price_unit);
        $this->conceptoAtributos->Importe          = (string)PatronDeDatosHelper::t_import($product->gross_sub_total);
        $this->conceptoAtributos->Descuento        = (string)PatronDeDatosHelper::t_import($product->discount);
        $this->conceptoAtributos->ObjetoImp        = $product->c_objeto_impuesto;


        return $this->conceptoAtributos;
    }

    private function getTraslado(Product $product): null|\App\Services\SAT\InvoiceSatData\ImpuestoTrasladoAtributos
    {
        $this->impuestoTrasladoAtributos->Base       = (string)PatronDeDatosHelper::t_import($product->sub_total);
        $this->impuestoTrasladoAtributos->Impuesto   = $product->c_impuesto;
        $this->impuestoTrasladoAtributos->TipoFactor = $product->c_tipo_factor;

        if ($product->c_tipo_factor !== 'Exento') {
            $this->impuestoTrasladoAtributos->TasaOCuota = $product->c_tasa_o_cuota;
            $this->impuestoTrasladoAtributos->Importe    = (string)PatronDeDatosHelper::t_import($product->tax);
        }

        return $this->impuestoTrasladoAtributos;
    }
}
