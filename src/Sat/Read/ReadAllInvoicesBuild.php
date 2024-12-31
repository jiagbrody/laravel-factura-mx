<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Read;

use JiagBrody\LaravelFacturaMx\Services\DataBase\DatabaseService;

readonly class ReadAllInvoicesBuild
{
    protected DatabaseService $databaseService;

    public function __construct()
    {
        $this->databaseService = new DatabaseService();
    }

    public function getAll()
    {
        return $this->databaseService->getAllSimpleRelationData()->paginate();
    }

    public function getBuilder(): \Illuminate\Database\Query\Builder
    {
        return $this->databaseService->getAllSimpleRelationData();
    }
}
