<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => str()->orderedUuid(),
            'subtotal' => 9999,
            'totalweight' => 100,
            'grandtotal' => 99999,
            'status' => collect(['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'completed'])->random()
        ];
    }
}
