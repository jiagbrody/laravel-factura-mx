<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use JiagBrody\LaravelFacturaMx\Exceptions\CfdiPreValidationException;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHelperAbstract;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;

class GenericCreator extends CfdiHelperAbstract
{
    use AddComplementoImpuestosLocalesTrait;

    public function __construct(InvoiceCompany $invoiceCompany)
    {
        $this->companyHelper = new InvoiceCompanyHelper($invoiceCompany);
        parent::__construct();
    }

    /**
     * Sella y arma el CFDI.
     *
     * @param  bool|null  $validate  Anula la validación local para ESTA llamada:
     *                               pásalo en false para builds provisionales
     *                               (p. ej. vistas previas con datos incompletos
     *                               que el usuario completará después). Con null
     *                               (default) manda el config "pre_validate_cfdi".
     */
    public function build(?bool $validate = null): CreateBuild
    {
        $this->creatorCfdi->addSumasConceptos(null, 2);
        $this->creatorCfdi->moveSatDefinitionsToComprobante();
        $this->creatorCfdi->addSello($this->credential->privateKey()->pem(), $this->credential->privateKey()->passPhrase());

        // Validación local (XSD + reglas SAT de cfdiutils) antes de guardar el
        // borrador: cada CFDI malformado que llega al PAC quema un intento de
        // timbrado y registra una incidencia evitable. Requiere los recursos
        // XSLT/XSD del SAT en "sat_local_resource_path" (se descargan solos la
        // primera vez).
        $shouldValidate = $validate ?? (bool) config('jiagbrody-laravel-factura-mx.pre_validate_cfdi', true);

        if ($shouldValidate) {
            $findings = $this->creatorCfdi->validate();
            if ($findings->hasErrors()) {
                throw CfdiPreValidationException::fromAsserts($findings);
            }
        }

        return new CreateBuild(
            xmlContent: $this->creatorCfdi->asXml(),
            companyHelper: $this->companyHelper,
            attributeAssembly: $this->attributeAssembly
        );
    }
}
