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

    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            // 'role' => 'required'
        ]);

        try{
            
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],    //Hash::make($validated['password']),
                'role' => 'user' //$validated['role'],
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $user->createToken('api-token')->plainTextToken
            ], 201);
        }
        catch(e)
        {
            console.log(e);
        }

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

    public function getUser(Request $request)
    {
        if (!$request->has('email')) {
            return response()->json(['success' => false, 'error' => 'Email is required'], 400);
        }

        // // Validate the email
        // $request->validate([
        //     'email' => 'required|email|exists:users,email'
        // ]);

        // Fetch the user from the database
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }

        // Return user data as JSON
        return response()->json([
            'success' => true,
            'message' => 'User found!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
}
