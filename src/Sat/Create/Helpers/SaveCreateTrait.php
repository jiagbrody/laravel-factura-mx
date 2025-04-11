<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use Illuminate\Support\Collection;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTaxTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceBalance;
use JiagBrody\LaravelFacturaMx\Models\InvoiceComplementLocalTax;
use JiagBrody\LaravelFacturaMx\Models\InvoiceComplementLocalTaxDetail;
use JiagBrody\LaravelFacturaMx\Models\InvoiceIncome;
use JiagBrody\LaravelFacturaMx\Models\InvoiceRelationship;
use JiagBrody\LaravelFacturaMx\Models\InvoiceTax;
use JiagBrody\LaravelFacturaMx\Models\InvoiceTaxDetail;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ConceptoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoRetenidoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ImpuestoTrasladoAtributos;
use JiagBrody\LaravelFacturaMx\Sat\Rules\ComprobanteDeIngresoRuleHelper;

trait SaveCreateTrait
{

}
