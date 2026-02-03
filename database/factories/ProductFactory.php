<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->name(),
            "category" => $this->faker->word(),
            "current_stock" => $this->faker->randomNumber(),
            "price" => $this->faker->randomFloat(2, 1, 1000),
            "currency" => $this->faker->currencyCode(),
            "description" => $this->faker->paragraph(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }


    public function no_category()
    {
        return $this->state(fn(array $attributes) => [
            "category" => null
        ]);
    }
}
