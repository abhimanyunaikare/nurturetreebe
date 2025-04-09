<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushToken;

class PushTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $pushToken = PushToken::updateOrCreate(
            ['user_id' => $request->user_id],
            ['token' => $request->token]
        );

        return response()->json(['success' => true, 'data' => $pushToken]);
    }
}
