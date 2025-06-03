<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'uuid' => 'required|uuid|unique:users,uuid',
            'name' => 'required|string',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user = User::create($request->only(['uuid', 'name', 'gender']));
        return response()->json(['message' => 'User created', 'data' => $user], 201);
    }

    public function me(Request $request)
    {
        $uuid = $request->get('user')['uuid'];
        $user = User::where('uuid', $uuid)->first();

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        return response()->json($user);
    }

    public function update(Request $request)
    {
        $uuid = $request->get('user')['uuid'];
        $user = User::where('uuid', $uuid)->first();

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $this->validate($request, [
            'name' => 'sometimes|required|string',
            'gender' => 'sometimes|in:male,female,other',
        ]);

        $user->update($request->only(['name', 'gender']));

        return response()->json(['message' => 'User updated', 'data' => $user]);
    }

    public function delete(Request $request)
    {
        $uuid = $request->get('user')['uuid'];
        $user = User::where('uuid', $uuid)->first();

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}

