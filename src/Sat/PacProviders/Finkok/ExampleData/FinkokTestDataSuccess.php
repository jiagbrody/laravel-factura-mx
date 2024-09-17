<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\ExampleData;

final readonly class FinkokTestDataSuccess
{
    public string $xml;

    public string $UUID;

    public string $Fecha;

    public string $CodEstatus;

    public string $SatSeal;

    public array $Incidencias;

    public string $NoCertificadoSAT;

    public function __invoke(): self
    {
        $this->xml = '<?xml version="1.0" encoding="utf-8"?><cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/4" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:implocal="http://www.sat.gob.mx/implocal" xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd" Version="4.0" NoCertificado="30001000000500003416" Certificado="MIIFsDCCA5igAwIBAgIUMzAwMDEwMDAwMDA1MDAwMDM0MTYwDQYJKoZIhvcNAQELBQAwggErMQ8wDQYDVQQDDAZBQyBVQVQxLjAsBgNVBAoMJVNFUlZJQ0lPIERFIEFETUlOSVNUUkFDSU9OIFRSSUJVVEFSSUExGjAYBgNVBAsMEVNBVC1JRVMgQXV0aG9yaXR5MSgwJgYJKoZIhvcNAQkBFhlvc2Nhci5tYXJ0aW5lekBzYXQuZ29iLm14MR0wGwYDVQQJDBQzcmEgY2VycmFkYSBkZSBjYWxpejEOMAwGA1UEEQwFMDYzNzAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBDSVVEQUQgREUgTUVYSUNPMREwDwYDVQQHDAhDT1lPQUNBTjERMA8GA1UELRMIMi41LjQuNDUxJTAjBgkqhkiG9w0BCQITFnJlc3BvbnNhYmxlOiBBQ0RNQS1TQVQwHhcNMjMwNTE4MTE0MzUxWhcNMjcwNTE4MTE0MzUxWjCB1zEnMCUGA1UEAxMeRVNDVUVMQSBLRU1QRVIgVVJHQVRFIFNBIERFIENWMScwJQYDVQQpEx5FU0NVRUxBIEtFTVBFUiBVUkdBVEUgU0EgREUgQ1YxJzAlBgNVBAoTHkVTQ1VFTEEgS0VNUEVSIFVSR0FURSBTQSBERSBDVjElMCMGA1UELRMcRUtVOTAwMzE3M0M5IC8gVkFEQTgwMDkyN0RKMzEeMBwGA1UEBRMVIC8gVkFEQTgwMDkyN0hTUlNSTDA1MRMwEQYDVQQLEwpTdWN1cnNhbCAxMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtmecO6n2GS0zL025gbHGQVxznPDICoXzR2uUngz4DqxVUC/w9cE6FxSiXm2ap8Gcjg7wmcZfm85EBaxCx/0J2u5CqnhzIoGCdhBPuhWQnIh5TLgj/X6uNquwZkKChbNe9aeFirU/JbyN7Egia9oKH9KZUsodiM/pWAH00PCtoKJ9OBcSHMq8Rqa3KKoBcfkg1ZrgueffwRLws9yOcRWLb02sDOPzGIm/jEFicVYt2Hw1qdRE5xmTZ7AGG0UHs+unkGjpCVeJ+BEBn0JPLWVvDKHZAQMj6s5Bku35+d/MyATkpOPsGT/VTnsouxekDfikJD1f7A1ZpJbqDpkJnss3vQIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAFaUgj5PqgvJigNMgtrdXZnbPfVBbukAbW4OGnUhNrA7SRAAfv2BSGk16PI0nBOr7qF2mItmBnjgEwk+DTv8Zr7w5qp7vleC6dIsZFNJoa6ZndrE/f7KO1CYruLXr5gwEkIyGfJ9NwyIagvHHMszzyHiSZIA850fWtbqtythpAliJ2jF35M5pNS+YTkRB+T6L/c6m00ymN3q9lT1rB03YywxrLreRSFZOSrbwWfg34EJbHfbFXpCSVYdJRfiVdvHnewN0r5fUlPtR9stQHyuqewzdkyb5jTTw02D2cUfL57vlPStBj7SEi3uOWvLrsiDnnCIxRMYJ2UA2ktDKHk+zWnsDmaeleSzonv2CHW42yXYPCvWi88oE1DJNYLNkIjua7MxAnkNZbScNw01A6zbLsZ3y8G6eEYnxSTRfwjd8EP4kdiHNJftm7Z4iRU7HOVh79/lRWB+gd171s3d/mI9kte3MRy6V8MMEMCAnMboGpaooYwgAmwclI2XZCczNWXfhaWe0ZS5PmytD/GDpXzkX0oEgY9K/uYo5V77NdZbGAjmyi8cE2B2ogvyaN2XfIInrZPgEffJ4AB7kFA2mwesdLOCh0BLD9itmCve3A1FGR4+stO2ANUoiI3w3Tv2yQSg4bjeDlJ08lXaaFCLW2peEXMXjQUk7fmpb5MNuOUTW6BE=" Serie="PATIENT" Folio="2" Fecha="2024-01-24T10:50:05" FormaPago="01" SubTotal="3027.00" Descuento="0.00" Moneda="MXN" Total="2936.19" TipoDeComprobante="I" Exportacion="01" MetodoPago="PUE" LugarExpedicion="63732" Sello="OhdqPU7VBm1zYTd0LTC7wUrAOS6kS4HMdHjJZo5FQa2dMsfP6seNfhh9Q90JFHvUmMc4MzXj6roF71sgvl4IN+/xklormvQywpzMgwY7JpOPiLKoUhYg5Gob7/VnS6g9r6D2VDX60LIB1T+xgNS50MRVYB9MY3PrS8SsAQYOqFCDxpOuP30faUt3kUnOhPkHGLBw2URx2ZIB74GSuXZXVXt4PZwFXrb6ufXEhtqzPuYmCrd8zrmuViiUAl/MNdH9uyXFI8wWp7Ws2lX464qenQ00d0Gs/3ONKFr03PMTF+8qTr9xdQ0w59oyap+biZslhZp3TpUo+XOGpo8nEdwaqg=="><cfdi:Emisor Rfc="EKU9003173C9" Nombre="ESCUELA KEMPER URGATE" RegimenFiscal="601"/><cfdi:Receptor Rfc="CUSC850516316" Nombre="CESAR OSBALDO CRUZ SOLORZANO" DomicilioFiscalReceptor="45638" RegimenFiscalReceptor="616" UsoCFDI="S01"/><cfdi:Conceptos><cfdi:Concepto ClaveProdServ="85101501" NoIdentificacion="7" Cantidad="1" ClaveUnidad="E48" Descripcion="HONORARIOS MEDICOS POR VALORACION DE URGENCIA" ValorUnitario="3027" Importe="3027" Descuento="0" ObjetoImp="02"><cfdi:Impuestos><cfdi:Traslados><cfdi:Traslado Base="3027" Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.000000" Importe="0"/></cfdi:Traslados></cfdi:Impuestos></cfdi:Concepto></cfdi:Conceptos><cfdi:Impuestos TotalImpuestosTrasladados="0.00"><cfdi:Traslados><cfdi:Traslado Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.000000" Importe="0.00" Base="3027.00"/></cfdi:Traslados></cfdi:Impuestos><cfdi:Complemento><implocal:ImpuestosLocales version="1.0" TotaldeRetenciones="90.81" TotaldeTraslados="0.00"><implocal:RetencionesLocales ImpLocRetenido="Impuesto Cedular" TasadeRetencion="3.00" Importe="90.81"/></implocal:ImpuestosLocales><tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" xsi:schemaLocation="http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd" Version="1.1" SelloCFD="OhdqPU7VBm1zYTd0LTC7wUrAOS6kS4HMdHjJZo5FQa2dMsfP6seNfhh9Q90JFHvUmMc4MzXj6roF71sgvl4IN+/xklormvQywpzMgwY7JpOPiLKoUhYg5Gob7/VnS6g9r6D2VDX60LIB1T+xgNS50MRVYB9MY3PrS8SsAQYOqFCDxpOuP30faUt3kUnOhPkHGLBw2URx2ZIB74GSuXZXVXt4PZwFXrb6ufXEhtqzPuYmCrd8zrmuViiUAl/MNdH9uyXFI8wWp7Ws2lX464qenQ00d0Gs/3ONKFr03PMTF+8qTr9xdQ0w59oyap+biZslhZp3TpUo+XOGpo8nEdwaqg==" NoCertificadoSAT="30001000000500003456" RfcProvCertif="CVD110412TF6" UUID="A1FF0B14-C976-5142-831E-7F870BBC2601" FechaTimbrado="2024-01-24T10:50:07" SelloSAT="Aqi+SiIc543/gLFgF2t0QT138n0sTpGueXpspx4ZOpa8VHYSybMQvsKNqRNn4vnr4lt6F7xqJXWT/C1RyFSEPxuHHCuAEgbSj1Y5eG4Wewssy/l+k/XjZNEDUd9KYRa3W8D0XxVykRD+6YUulpYtokEP/xt41PH0x4P8lC+rQ4TUaGS150P0AgwLTqYHAKPp0zOew6ozcyW3tFdTCSbtQJ0M6sIinGRkTErivIE+CZI8Y2ZGRjCoalyW8RTi9fMIU+wzBtOrUUBQ72ksMsFqN8do/3FGfZ6SmYt2et2LlwF2JGR/eFnybIV3bsI/scnbDu57l83i8q0xZMzE8cw6fw=="/></cfdi:Complemento></cfdi:Comprobante>';
        $this->UUID = 'A1FF0B14-C976-5142-831E-7F870BBC2601';
        $this->Fecha = date('Y-m-d\TH:i:s');
        $this->CodEstatus = 'Comprobante timbrado satisfactoriamente';
        $this->SatSeal = 'Aqi+SiIc543/gLFgF2t0QT138n0sTpGueXpspx4ZOpa8VHYSybMQvsKNqRNn4vnr4lt6F7xqJXWT/C1RyFSEPxuHHCuAEgbSj1Y5eG4Wewssy/l+k/XjZNEDUd9KYRa3W8D0XxVykRD+6YUulpYtokEP/xt41PH0x4P8lC+rQ4TUaGS150P0AgwLTqYHAKPp0zOew6ozcyW3tFdTCSbtQJ0M6sIinGRkTErivIE+CZI8Y2ZGRjCoalyW8RTi9fMIU+wzBtOrUUBQ72ksMsFqN8do/3FGfZ6SmYt2et2LlwF2JGR/eFnybIV3bsI/scnbDu57l83i8q0xZMzE8cw6fw==';
        $this->Incidencias = [];
        $this->NoCertificadoSAT = '30001000000500003456';

        return $this;
    }
}
