<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait AddReadableDatesHelperTrait
{
    protected function createdAtFormat(): Attribute
    {
        return Attribute::make(get: fn() => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
            ->format('m/d/Y'));
    }

    protected function createdAtHuman(): Attribute
    {
        return Attribute::make(get: fn() => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
            ->diffForHumans());
    }

    protected function updatedAtFormat(): Attribute
    {
        return Attribute::make(get: fn() => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
            ->format('m/d/Y'));
    }

    protected function updatedAtHuman(): Attribute
    {
        return Attribute::make(get: fn() => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
            ->diffForHumans());
    }
}
