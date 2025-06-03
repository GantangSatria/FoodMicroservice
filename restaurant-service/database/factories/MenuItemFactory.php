<?php

namespace Database\Factories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->word(),
            'slug' => Str::slug($this->faker->unique()->words(2, true)),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'is_available' => $this->faker->boolean(90),
        ];
    }
}
