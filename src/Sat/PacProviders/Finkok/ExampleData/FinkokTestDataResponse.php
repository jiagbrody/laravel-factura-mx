<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\ExampleData;

use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;

class FinkokTestDataResponse
{
    public FinkokTestDataSuccess $quick_stampResult;

    public function success(): self
    {
        $this->quick_stampResult = (new FinkokTestDataSuccess)();

        return $this;
    }

    public function getResponse(): PacStampResponse
    {
        $response = new PacStampResponse;
        $response->setFullResponse($this);

        return $response;
    }
}
