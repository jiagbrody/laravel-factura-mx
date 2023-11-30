<?php

namespace JiagBrody\LaravelFacturaMx\Commands;

use Illuminate\Console\Command;

class LaravelFacturaMxCommand extends Command
{
    public $signature = 'laravel-factura-mx';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
