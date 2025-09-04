<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Helper\ConvertXmlContentToObjectHelper;

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

    public function getXmlContent(): string
    {
        return $this->xmlContent;
    }
}
