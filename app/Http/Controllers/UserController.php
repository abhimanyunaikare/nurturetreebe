<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Cache;


/**
 * @group User management
 *
 * APIs for managing users
 */

class UserController extends Controller
{
    // ✅ Fetch all users
    public function index()
    {
        
        $users = Cache::remember('users', 60, function () {
            return User::paginate(10);
        });

        return ApiResponse::success('Users fetched successfully!', $users);
    }

    // ✅ Get a single user by ID
    public function show($id)
    {
        $user = User::findOrFail($id);

        return ApiResponse::success('User fetched successfully!', $user);
    }

    // ✅ Create a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => 'password', // bcrypt($request->password), // Encrypt password
            'role' => 'user',

        ]);

        return response()->json(['message' => 'User created successfully!', 'user' => $user], 201);
    }

    // ✅ Update user details
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($request->all());

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    // ✅ Delete a user
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
