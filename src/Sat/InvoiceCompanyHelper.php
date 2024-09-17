<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;

class InvoiceCompanyHelper
{
    public int $id;

    public string $rfc;

    public string $nombre;

    public string $regimenFiscal;

    public string $facAtrAdquirente;

    public string $certificatePath;

    public string $keyPath;

    public string $passPhrase;

    public string $serialNumber;

    public bool $pacEnvironmentProduction;

    public function __construct(InvoiceCompany $invoiceCompany)
    {
        $invoiceCompany->refresh(); //It probably comes from the static method: "getProtectedData()"
        $folder = config('jiagbrody-laravel-factura-mx.sat_files_path');

        $this->pacEnvironmentProduction = config('jiagbrody-laravel-factura-mx.pac_environment_production');
        $this->id = $invoiceCompany->id;
        $this->rfc = $invoiceCompany->rfc;
        $this->nombre = $invoiceCompany->nombre;
        $this->regimenFiscal = $invoiceCompany->regimen_fiscal;
        $this->certificatePath = $folder.$invoiceCompany->certificate_path;
        $this->keyPath = $folder.$invoiceCompany->key_path;
        $this->passPhrase = $invoiceCompany->pass_phrase;
        $this->serialNumber = $invoiceCompany->serial_number;

        return $this;
    }
}
