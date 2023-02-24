<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipping>
 */
class ShippingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'courier' => collect(['JNE', 'TIKI', 'POS'])->random(),
            'service' => collect(['Oke', 'Regular', 'Express'])->random(),
            'trackingnumber' => fake()->regexify('[0-9]{16}'),
            'shippingcost' => 2000
        ];
    }
}
