<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Cancel;

use App\Enums\CfdiCancelTypeEnum;
use App\Models\CfdiCancel;
use App\Models\Invoice;
use App\Services\PAC\Providers\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Sat\Helper\XmlFileSatHelperBuilder;

class CancelConcrete
{
    protected CfdiCancel $cancel;

    public function __construct(protected Invoice $invoice, protected PacCancelResponse $pacCancelResponse)
    {
    }

    public function createCfdiCancel(CfdiCancelTypeEnum $enum): self
    {
        $this->cancel = $this->invoice->cfdi->cfdiCancel()->create([
            'cfdi_cancel_type_id' => $enum->value,
            'estatus_uuid' => $this->pacCancelResponse->estatusUUID,
            'estatus_cancelacion' => $this->pacCancelResponse->estatusCancelacion,
        ]);

        return $this;
    }

    public function generateAcuse(): self
    {
        if ($this->pacCancelResponse->acuse) {

            (new XmlFileSatHelperBuilder($this->invoice))
                ->updateModel($this->cancel)
                ->updatePath('cancels/'.date('Y').'/'.date('m').'/'.date('d'))
                ->updateFileName($this->invoice->cfdi->uuid)
                ->generate($this->pacCancelResponse->acuse);
        }

        return $this;
    }
}
