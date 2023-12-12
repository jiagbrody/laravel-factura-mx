<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use PhpCfdi\CfdiToJson\JsonConverter;

final class ConvertXmlContentToObjectHelper
{
    static public function make(string $xmlContent, $associative = null): mixed
    {
        try {
            $json = JsonConverter::convertToJson($xmlContent);
        } catch (\Exception $e) {
            abort(500, 'Ocurrió un error en "ConvertXmlContentToObjectHelper": ' . $e->getMessage());
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            abort(500, 'Ha ocurrido un error al convertir en Json el contenido XML');
        }

        return json_decode($json, $associative);
    }
}
