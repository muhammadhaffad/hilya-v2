<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductItem>
 */
class ProductItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'gender' => collect(['koko', 'gamis'])->random(),
            'age' => collect(['anak-anak', 'remaja', 'dewasa'])->random(),
            'size' => collect(['1', '3', '6', '9', 'M', 'L', 'XL'])->random(),
            'color' => fake()->colorName(),
            'price' => rand(120000,250000),
            'stock' => rand(5, 50)
        ];
    }
}
