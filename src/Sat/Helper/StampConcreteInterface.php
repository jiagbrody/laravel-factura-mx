<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

interface StampConcreteInterface
{
    public function removeInvoiceDraft(): self;

    public function createCfdi(): self;

    public function generateDocumentsFromCfdi(): self;
}
