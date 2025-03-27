<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tree;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Cache;


/**
 * @group Tree management
 *
 * APIs for managing Trees
 */

class TreeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'species' => 'required|string',
            'lat' => 'required|numeric',            
            'long' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        $tree = Tree::create([
            'user_id' => auth()->id(),  // Attach logged-in user
            'species' => $request->species,
            'lat' => $request->lat,
            'long' => $request->long,
            'user_id' => $request->user_id
        ]);

        return ApiResponse::success('Tree planted successfully!', $tree, 201);
    }

    public function index()
    {
        
        $trees = Cache::remember('trees', 60, function () {
            return Tree::with('user')->paginate(10);
        });

        return ApiResponse::success('Trees fetched successfully!', $trees);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'species' => 'sometimes|string',
            'lat' => 'sometimes|numeric',
            'long' => 'sometimes|numeric',
            'health_status' => 'sometimes|string',
        ]);

        $tree = Tree::findOrFail($id);
        
        // Ensure only the owner or admin can update
        if ($tree->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $tree->update($request->only(['species', 'lat', 'long', 'health_status']));
        Cache::forget('trees'); // Clear cache after update

        return ApiResponse::success('Tree updated successfully!', $tree);
    }

    public function destroy($id)
    {
        $tree = Tree::findOrFail($id);

        // Ensure only the owner can delete
        if ($tree->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $tree->delete();
        Cache::forget('trees');

        return ApiResponse::success('Tree deleted successfully!');
    }

    public function show($id)
    {
        $tree = Tree::with('user')->findOrFail($id);

        return ApiResponse::success('Tree fetched successfully!', $tree);
    }

    public function nurture($id)
    {
        $tree = Tree::findOrFail($id); // Find tree or return 404

        $tree->update([
            'last_watered' => now(), // Update the last_watered timestamp
            'health_status' => 'Thriving' // Optional: Change health status
        ]);

        return ApiResponse::success('Tree nurtured successfully!', $tree);
    }

    

}
