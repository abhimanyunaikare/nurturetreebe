<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tree;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


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
            'photo_uri' => 'nullable|string', 
        ]);

        $tree = Tree::create([
            'user_id' => auth()->id(),  // Attach logged-in user
            'species' => $request->species,
            'lat' => $request->lat,
            'long' => $request->long,
            'age' => $request->age,
            'interval' => $request->interval,
            'sunlight' => $request->sunlight,
            'water_qty' => $request->water_qty,
            'created_by' => $request->created_by,
            'watered_by' => $request->created_by,
            'user_id' => 1,
            'last_watered' => now(),
            'photo_uri' => $request->photo_uri

        ]);

        Cache::forget('trees');

        return ApiResponse::success('Tree planted successfully!', $tree, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $tree = Tree::findOrFail($id);
        $tree->status = $request->status;
        $tree->save();

        Cache::forget('trees');

        return response()->json(['message' => 'Tree status updated']);
    }

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $userId = $request->query('user_id');

        // Create a unique cache key for different filters
        $cacheKey = "trees_{$filter}_" . ($userId ?? 'all');
        
        $trees = Cache::remember($cacheKey, 60, function () use ($filter, $userId){
            $query = Tree::with(['creator', 'waterer']);

            if ($filter === 'my' && $userId) {
                $query->where('created_by', $userId);
            }
            else if ($filter === 'dead') {
                $query->where('status', 0);
            } else {
                $query->where('status', 1);
            }

            return $query
                    ->paginate(100)
                    ->through(function ($tree) {
                        $daysSinceCreated = Carbon::parse($tree->created_at)->diffInDays(now());
                        $tree->age = (int) ($tree->age + $daysSinceCreated);
                        return $tree;
                    });
        });

        // Add water_requirement status
        // $trees->getCollection()->transform(function ($tree) {
        //     // Calculate water requirement
        //     $tree->water_requirement = $this->calculateWaterRequirement($tree);
        //     return $tree;
        // });

        // Filter trees that need watering

        if ($filter != 'my')
        {
            $trees = $trees->filter(function ($tree) {
                return $this->calculateWaterRequirement($tree) === 1;
            });
        }

        return ApiResponse::success('Trees fetched successfully!', $trees);
    }

    /**
     * Determine if a tree needs watering
     */
    private function calculateWaterRequirement($tree)
    {
        // If last_watered is null, it needs watering
        if (!$tree->last_watered) {
            return 1;
        }

        // Convert last_watered to Carbon instance
        $lastWatered = Carbon::parse($tree->last_watered);
        $interval = $tree->interval ?? 0; // Ensure interval is numeric
        $nextWateringDate = $lastWatered->addDays($interval);

        // Compare with today's date
        return Carbon::today()->greaterThan($nextWateringDate) ? 1 : 0;
    }


    public function showByUser(Request $request)
    {
        
        $trees = Cache::remember('trees', 60, function () {
            return Tree::with(['creator', 'waterer'])
                    ->where('created_by', $request->user);
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

        $tree->update($request->only(['species', 'lat', 'long', 'health_status', 'age']));
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

    public function nurture(Request $request,$id)
    {
        $tree = Tree::findOrFail($id); // Find tree or return 404
        
        $tree->update([
            'last_watered' => now(), 
            'health_status' => 'Thriving',
            'watered_by' => $request->watered_by, // Store who watered the tree
        ]);

        return ApiResponse::success('Tree nurtured successfully!', $tree);
    }

    

}
