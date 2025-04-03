<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


/**
 * @group Auth management
 *
 * APIs for managing Auth
 */


class AuthController extends Controller
{
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

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $user->createToken('api-token')->plainTextToken
        ]);
    }
    
    public function user(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        return $request;
    
        // Fetch user from DB
        $user = User::where('email', $request->input('email'))->firstOrFail();
    
        return response()->json([
            'success' => true,
            'message' => 'User found!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $user->createToken('api-token')->plainTextToken
        ]);
    }
    

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully!'
        ]);
    }
}


