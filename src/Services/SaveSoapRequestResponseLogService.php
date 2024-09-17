<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services;

use Illuminate\Support\Facades\Log;

class SaveSoapRequestResponseLogService
{
    public function make($client, $title, $logName): void
    {
        Log::build([
            'driver' => 'single',
            'path' => storage_path("logs/{$logName}.log"),
        ])->info($title, [
            'request' => $client->__getLastRequest(),
            'response' => $client->__getLastResponse(),
        ]);
    }
}
