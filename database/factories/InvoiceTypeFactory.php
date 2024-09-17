<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
