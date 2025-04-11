<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use Illuminate\Database\Eloquent\Model;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Helper\ConvertXmlContentToObjectHelper;
use JiagBrody\LaravelFacturaMx\Sat\Helper\GeneratePdfDocumentFromXmlObjectForIngresoHelper;

trait CommonMethodsForBuildersTrait
{
    public function getArrayOfXmlContentBeforeSaving(): array
    {
        return ConvertXmlContentToObjectHelper::make($this->xmlContent, true);
    }

    public function getObjectOfXmlContentBeforeSaving(): object
    {
        return ConvertXmlContentToObjectHelper::make($this->xmlContent);
    }

    public function getAttributeAssembly(): AttributeAssembly
    {
        return $this->attributeAssembly;
    }
}
