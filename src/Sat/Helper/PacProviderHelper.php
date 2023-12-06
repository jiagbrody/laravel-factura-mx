<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use App\Models\Invoice;
use App\Services\PAC\Providers\Finkok\FinkokPacInterface;

class PacProviderHelper
{
    protected FinkokPacInterface $pacProvider;

    /*
     * SELECCIÓN DEL PAC A USAR ESTE PODRIA SER CAMBIADO DINAMICAMENTE
     */
    public function __construct(Invoice $invoice)
    {
        $this->pacProvider = new FinkokPacInterface($invoice);
    }
}
