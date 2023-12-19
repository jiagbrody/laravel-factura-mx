<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use App\Enums\CfdiStatusEnum;
use App\Enums\InvoiceCfdiTypeEnum;
use App\Services\Logs\SaveSoapRequestResponseLogService;
use Exception;
use Illuminate\Support\Facades\DB;
use SoapClient;

trait StatusTrait
{
    private function getStatusCfdiSat(): array
    {
        if (!$this->invoice->cfdi->xmlInvoiceDocument) {
            abort(403, 'Cfdi timbrado pero no estan generados los documentos. Es necesario generarlos.');
        }

        $params = [
            "username"     => $this->usernameFinkok,
            "password"     => $this->passwordFinkok,
            "taxpayer_id"  => $this->invoice->invoiceDetail->emisor_rfc,
            "rtaxpayer_id" => $this->invoice->invoiceDetail->receptor_rfc,
            "uuid"         => $this->invoice->cfdi->uuid,
            "total"        => $this->invoice->invoiceDetail->total,
        ];

        try {
            $client   = new SoapClient($this->cancelUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall("get_sat_status", [$params]);
            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:get_sat_status', 'cfdi_finkok_get_sat_status');
        } catch (exception $e) {
            abort(422, $e->getMessage());
        }

        $sat                = $response->get_sat_statusResult->sat;
        $estatusCancelacion = (isset($sat->EstatusCancelacion)) ? "<strong>EstatusCancelacion:</strong> {$sat->EstatusCancelacion}<br>" : '';

        if ($this->invoice->cfdi->cfdi_status_id !== CfdiStatusEnum::CANCELED->value) {
            $this->saveStatusChance($sat);
        }

        return [
            'title'       => 'Respuesta del SAT',
            'description' => "
            <strong>DetallesValidacionEFOS:</strong> {$sat->DetallesValidacionEFOS}<br>
            <strong>ValidacionEFOS:</strong> {$sat->ValidacionEFOS}<br><br>
            <strong>EsCancelable:</strong> {$sat->EsCancelable}<br>
            <strong>CodigoEstatus:</strong> {$sat->CodigoEstatus}<br>
            <strong>Estado:</strong> {$sat->Estado}<br>
            $estatusCancelacion
            ",
        ];
    }

    private function saveStatusChance($sat): void
    {
        DB::transaction(function () use ($sat) {
            if ($sat->Estado === 'Cancelado') {
                # ACTUALIZO EL NOMBRE DEL REGISTRO DE LA SOLICITUD PREVIA DEL PAC
                $this->invoice->cfdi->cfdi_status_id = CfdiStatusEnum::CANCELED->value;
                $this->invoice->cfdi->save();

                # ACTUALIZO EL VALIDADOR ABSOLUTO EN EL SISTEMA
                $this->invoice->is_canceled = true;
                $this->invoice->save();

                # QUITO LA RELACIÓN DE LOS PRODUCTOS DEL INVOICE PARA PODER GESTIONARLOS NUEVAMENTE (O HACER LA SUBSTITUCION DE LA FACTURA).
                if ($this->invoice->invoice_cfdi_type_id === InvoiceCfdiTypeEnum::INGRESO->value) {
                    $this->invoice->statementDetails()->detach();
                }

                //TODO: CHECAR ESTA PARTE COMO SE DEBE QUEDAR BIEN LA CANCELACION DEL PAGO, SE SUPONE QUE SOLO ES UN CAMBI DE REGISTRO, NO RECUERDO POR QUE EL "LOOP" PARA ACTUALIZAR
                if ($this->invoice->invoice_cfdi_type_id === InvoiceCfdiTypeEnum::PAGO->value) {
                    $this->invoice->invoicePayments->each(function ($item) {
                        $item->invoicePaymentDocuments()->update(['is_active' => false]);
                    });
                }
            }

            if ($this->invoice->cfdi->cfdiCancel && isset($sat->EstatusCancelacion)) {
                $this->invoice->cfdi->cfdiCancel->estatus_cancelacion = $sat->EstatusCancelacion;
                $this->invoice->cfdi->cfdiCancel->save();
            }
        });
    }
}
