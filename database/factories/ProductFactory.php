<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->text(200),
            'availability' => collect(['ready', 'pre-order'])->random(),
            'ispromo' => collect([true, false])->random()
        ];
    }

    public function brand($min, $max) {
        return $this->state(function (array $attributes) use ($min, $max) {
            return [
                'product_brand_id' => rand($min, $max)
            ];
        });
    }
}
