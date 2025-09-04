<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use PhpCfdi\CfdiToJson\JsonConverter;

final class ConvertXmlContentToObjectHelper
{
    public function __construct(private string $xmlContent) {}

    public function makeObject()
    {
        return $this->convert(false);
    }

    public function makeArray()
    {
        return $this->convert(true);
    }

    private function convert(bool $associative)
    {
        try {
            $json = JsonConverter::convertToJson($this->xmlContent);
        } catch (\Exception $e) {
            abort(500, 'Ocurrió un error en "ConvertXmlContentToObjectHelper": '.$e->getMessage());
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            abort(500, 'Ha ocurrido un error al convertir en Json el contenido XML');
        }

        return json_decode($json, $associative);
    }

    public static function make(string $xmlContent, $associative = null): array|object
    {
        try {
            $json = JsonConverter::convertToJson($xmlContent);
        } catch (\Exception $e) {
            abort(500, 'Ocurrió un error en "ConvertXmlContentToObjectHelper": '.$e->getMessage());
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            abort(500, 'Ha ocurrido un error al convertir en Json el contenido XML');
        }

        return json_decode($json, $associative);
    }
}
