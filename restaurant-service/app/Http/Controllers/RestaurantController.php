<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index()
    {
        return response()->json(Restaurant::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $restaurant = Restaurant::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'is_active' => true
        ]);

        return response()->json($restaurant, 201);
    }

    public function show($uuid)
    {
        $restaurant = Restaurant::where('uuid', $uuid)->firstOrFail();
        return response()->json($restaurant);
    }
}
