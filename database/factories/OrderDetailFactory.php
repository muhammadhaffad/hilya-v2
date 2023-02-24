<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'qty' => rand(1, 3)
        ];
    }

    public function productDetail($max) {
        return $this->state(function (array $attributes) use ($max) {
            return [
                'product_detail_id' => fake()->unique(true)->numberBetween(1, $max)
            ];
        });
    }
}
