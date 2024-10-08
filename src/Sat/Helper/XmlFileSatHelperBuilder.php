<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use App\Services\Documentable\DocumentGenerateService;
use Exception;
use JiagBrody\LaravelFacturaMx\Models\Document;
use JiagBrody\LaravelFacturaMx\Models\Invoice;

final class XmlFileSatHelperBuilder
{
    private string $satPath;

    private mixed $model;

    private string $fileName;

    private string $path;

    public function __construct(Invoice $invoice)
    {
        $this->satPath = env('SAT_FOLDER_TO_SAVE_BILLING_FILES', '');
        $this->model = $invoice;
        $this->fileName = 'invoice-'.$invoice->id.'-draft';
        $this->path = $this->satPath.'/'.'drafts';
    }

    public function updateModel($value): static
    {
        $this->model = $value;

        return $this;
    }

    public function updateFileName($value): static
    {
        $this->fileName = $value;

        return $this;
    }

    public function updatePath($value): static
    {
        $this->path = $this->satPath.'/'.$value;

        return $this;
    }

    public function generate($xmlContent): Document
    {
        try {
            return (new DocumentGenerateService(model: $this->model, path: $this->path, fileName: $this->fileName.'.xml', extension: 'xml', mimeType: 'text/xml', fileContent: $xmlContent))->make();
        } catch (Exception $e) {
            abort(422, 'Error al generar documento XML: '.$e->getMessage());
        }
    }
}
