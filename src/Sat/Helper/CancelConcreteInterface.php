<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use App\Enums\CfdiCancelTypeEnum;

interface CancelConcreteInterface
{
    public function createCfdiCancel(CfdiCancelTypeEnum $enum): self;

    public function generateAcuse(): self;
}
