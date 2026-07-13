<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use JiagBrody\LaravelFacturaMx\Exceptions\FacturaMxException;
use PhpCfdi\CfdiToJson\JsonConverter;

final class ConvertXmlContentToObjectHelper
{
    public function __construct(private string $xmlContent) {}

    public function makeObject()
    {
        return self::make($this->xmlContent, false);
    }

    public function makeArray()
    {
        return self::make($this->xmlContent, true);
    }

    public static function make(string $xmlContent, $associative = null): array|object
    {
        if (trim($xmlContent) === '') {
            throw new FacturaMxException('El contenido XML del CFDI está vacío; no hay nada que convertir.');
        }

        try {
            $json = JsonConverter::convertToJson($xmlContent);
        } catch (\Throwable $e) {
            throw new FacturaMxException('No se pudo convertir el XML del CFDI a JSON: '.$e->getMessage(), 0, $e);
        }

        $decoded = json_decode($json, $associative);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new FacturaMxException('Ha ocurrido un error al decodificar el JSON del CFDI: '.json_last_error_msg());
        }

        return $decoded;
    }
}
