<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'address' => fake()->streetAddress(),
            'postcode' => fake()->postcode(),
            'price' => fake()->numberBetween(150000, 1500000),
            'property_type' => fake()->randomElement(['House', 'Flat', 'Apartment', 'Townhouse']),
            'bedrooms' => fake()->numberBetween(1, 5),
        ];
    }
}