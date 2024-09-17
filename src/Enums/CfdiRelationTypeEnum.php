<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Enums;

enum CfdiRelationTypeEnum: string
{
    case NOTA_DE_CREDITO = '01';
    case NOTA_DE_DEBITO = '02';
    case DEVOLUCION_DE_MERCANCIA = '03';
    case SUSTITUCION_CFDI_PREVIOS = '04';
    case TRASLADOS_DE_MERCANCIAS = '05';
    case FACTURA_GENERADA_TRASLADOS = '06';
    case CFDI_POR_APLICACION_DE_ANTICIPO = '07';

    public function id(): int
    {
        return match ($this) {
            self::NOTA_DE_CREDITO => 1,
            self::NOTA_DE_DEBITO => 2,
            self::DEVOLUCION_DE_MERCANCIA => 3,
            self::SUSTITUCION_CFDI_PREVIOS => 4,
            self::TRASLADOS_DE_MERCANCIAS => 5,
            self::FACTURA_GENERADA_TRASLADOS => 6,
            self::CFDI_POR_APLICACION_DE_ANTICIPO => 7,
        };
    }
}
