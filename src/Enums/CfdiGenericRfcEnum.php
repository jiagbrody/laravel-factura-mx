<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Enums;

enum CfdiGenericRfcEnum: string
{
    case NACIONAL   = 'XAXX010101000';
    case EXTRANJERO = 'XEXX010101000';

    public function getData(): array
    {
        return match ($this) {
            self::NACIONAL => [
                'rfc'                       => 'XAXX010101000',
                'nombre'                    => 'CLIENTE',
                'regimen_fiscal_receptor'   => '616',
                'uso_cfdi'                  => 'S01',
                'domicilio_fiscal_receptor' => '00000', # AL CREAR FACTURA ESTE SE ACTUALIZARÁ AL CODIGO POSTAL DEL "EMISOR".
                'residencia_fiscal'         => null,
                'num_reg_id_trib'           => null,
                'is_foreign_resident'       => false,
            ],
            self::EXTRANJERO => [
                'rfc'                       => 'XEXX010101000',
                'nombre'                    => 'CLIENT',
                'regimen_fiscal_receptor'   => '616',
                'uso_cfdi'                  => 'S01',
                'domicilio_fiscal_receptor' => '00000', # AL CREAR FACTURA ESTE SE ACTUALIZARÁ AL CODIGO POSTAL DEL "EMISOR".
                'residencia_fiscal'         => 'USA',
                'num_reg_id_trib'           => '',
                'is_foreign_resident'       => true,
            ],
        };
    }
}
