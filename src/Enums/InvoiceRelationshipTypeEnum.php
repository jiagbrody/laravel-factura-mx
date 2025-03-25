<?php

namespace JiagBrody\LaravelFacturaMx\Enums;

enum InvoiceRelationshipTypeEnum: int
{
    case NOTA_DE_CREDITO = 1;
    case NOTA_DE_DEBITO = 2;
    case DEVOLUCION_DE_MERCANCIA = 3;
    case SUSTITUCION_CFDI_PREVIOS = 4;
    case TRASLADOS_DE_MERCANCIAS = 5;
    case FACTURA_GENERADA_TRASLADOS = 6;
    case CFDI_POR_APLICACION_DE_ANTICIPO = 7;

    public static function getCatalog(): \Illuminate\Support\Collection {}

    public function getSatId(): string
    {
        return match ($this) {
            self::NOTA_DE_CREDITO => '01',
            self::NOTA_DE_DEBITO => '02',
            self::DEVOLUCION_DE_MERCANCIA => '03',
            self::SUSTITUCION_CFDI_PREVIOS => '04',
            self::TRASLADOS_DE_MERCANCIAS => '05',
            self::FACTURA_GENERADA_TRASLADOS => '06',
            self::CFDI_POR_APLICACION_DE_ANTICIPO => '07',
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::NOTA_DE_CREDITO => 'Nota de crédito de los documentos relacionados',
            self::NOTA_DE_DEBITO => 'Nota de débito de los documentos relacionados',
            self::DEVOLUCION_DE_MERCANCIA => 'Devolución de mercancía sobre facturas o traslados previos',
            self::SUSTITUCION_CFDI_PREVIOS => 'Sustitución de los CFDI previos',
            self::TRASLADOS_DE_MERCANCIAS => 'Traslados de mercancías facturados previamente',
            self::FACTURA_GENERADA_TRASLADOS => 'Factura generada por los traslados previos',
            self::CFDI_POR_APLICACION_DE_ANTICIPO => 'CFDI por aplicación de anticipo',
        };
    }
}
