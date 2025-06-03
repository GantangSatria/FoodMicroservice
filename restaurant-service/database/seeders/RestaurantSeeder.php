<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\MenuItem;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            $restaurant = Restaurant::create([
                'uuid' => (string) Str::uuid(),
                'name' => "Restaurant $i",
                'slug' => "restaurant-$i",
                'phone' => '08123456789',
                'address' => "Address $i",
                'is_active' => true,
            ]);

            for ($j = 1; $j <= 10; $j++) {
                MenuItem::create([
                    'uuid' => (string) Str::uuid(),
                    'restaurant_id' => $restaurant->id,
                    'name' => "Menu Item $j",
                    'slug' => "menu-item-$j",
                    'price' => rand(10000, 100000),
                    'is_available' => true,
                ]);
            }
        }
    }
}
