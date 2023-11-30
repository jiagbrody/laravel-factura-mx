<?php

namespace JiagBrody\LaravelFacturaMx\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JiagBrody\LaravelFacturaMx\LaravelFacturaMx
 */
class LaravelFacturaMx extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \JiagBrody\LaravelFacturaMx\LaravelFacturaMx::class;
    }
}
