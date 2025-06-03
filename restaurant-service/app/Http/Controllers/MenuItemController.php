<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function index($restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        return response()->json($restaurant->menuItems);
    }

    public function store(Request $request, $restaurantId)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $menuItem = MenuItem::create([
            'uuid' => Str::uuid(),
            'restaurant_id' => $restaurantId,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'price' => $data['price'],
            'is_available' => true
        ]);

        return response()->json($menuItem, 201);
    }

    public function show($uuid)
    {
        $menuItem = MenuItem::where('uuid', $uuid)->firstOrFail();
        return response()->json($menuItem);
    }
}
