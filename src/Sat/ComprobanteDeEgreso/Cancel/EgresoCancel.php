<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Cancel;

use App\Enums\CfdiCancelTypeEnum;
use App\Models\Invoice;
use App\Services\PAC\Providers\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Cancel\CancelCfdiInterface;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PacProviderHelper;

final class EgresoCancel extends PacProviderHelper implements CancelCfdiInterface
{
    public function __construct(protected Invoice $invoice, protected CfdiCancelTypeEnum $cfdiCancelTypeEnum, protected ?string $uuidReplace = null)
    {
        parent::__construct($this->invoice);
    }

    public function getPacResponse(): PacCancelResponse
    {
        $pacResponse = $this->pacProvider->cancelInvoice($this->cfdiCancelTypeEnum, $this->uuidReplace);

        if ($pacResponse->checkProcess) {
            $concrete = new EgresoCancelConcrete($this->invoice, $pacResponse);
            $concrete->createCfdiCancel($this->cfdiCancelTypeEnum)->generateAcuse();
        }

        return $pacResponse;
    }
}