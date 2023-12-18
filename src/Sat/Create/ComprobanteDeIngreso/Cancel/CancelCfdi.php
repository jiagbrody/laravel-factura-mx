<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso\Cancel;

use App\Enums\CfdiCancelTypeEnum;
use App\Models\Invoice;
use App\Services\PAC\Providers\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PacProviderHelper;

class CancelCfdi extends PacProviderHelper implements CancelCfdiInterface
{
    public function __construct(protected Invoice $invoice, protected CfdiCancelTypeEnum $cfdiCancelTypeEnum, protected ?string $uuidReplace = null)
    {
        parent::__construct($this->invoice);
    }

    public function getPacResponse(): PacCancelResponse
    {
        $pacResponse = $this->pacProvider->cancelInvoice($this->cfdiCancelTypeEnum, $this->uuidReplace);
        $this->runConcrete($pacResponse);

        return $pacResponse;
    }

    private function runConcrete(PacCancelResponse $pacResponse): void
    {
        if ($pacResponse->checkProcess) {
            $concrete = new CancelConcrete($this->invoice, $pacResponse);
            $concrete->createCfdiCancel($this->cfdiCancelTypeEnum)->generateAcuse();
        }
    }
}
