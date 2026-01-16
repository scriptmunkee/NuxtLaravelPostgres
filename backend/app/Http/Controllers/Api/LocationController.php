<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get all locations
     */
    public function index()
    {
        $locations = Location::orderBy('city')->get();

        return response()->json([
            'locations' => $locations,
        ]);
    }

    /**
     * Search locations by city or county
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $locations = Location::where('city', 'LIKE', "%{$query}%")
            ->orWhere('county', 'LIKE', "%{$query}%")
            ->orderBy('city')
            ->limit(20)
            ->get();

        return response()->json([
            'locations' => $locations,
        ]);
    }

    /**
     * Get a single location
     */
    public function show(Location $location)
    {
        return response()->json([
            'location' => $location,
        ]);
    }
}
