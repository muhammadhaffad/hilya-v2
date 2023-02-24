<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'bank' => collect(['BRI', 'BCA', 'BNI', 'Mandiri'])->random(),
            'vanumber' => fake()->regexify('[0-9]{16}'),
            'amount' => rand(40000,800000),
            'status' => collect(['settlement', 'pending', 'deny', 'cancel', 'expire'])->random(),
            'transactiontime' => fake()->date('Y-m-d H:i:s'),
            'settlementtime' => fake()->date('Y-m-d H:i:s')
        ];
    }
}
