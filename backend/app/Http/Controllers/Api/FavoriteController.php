<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Get all favorites for the authenticated user
     */
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['listing.user', 'listing.category', 'listing.breed', 'listing.location', 'listing.primaryImage'])
            ->latest()
            ->get();

        // Extract listings from favorites
        $listings = $favorites->map(function ($favorite) {
            $listing = $favorite->listing;
            $listing->favorited_at = $favorite->created_at;
            return $listing;
        });

        return response()->json([
            'favorites' => $listings,
            'total' => $listings->count(),
        ]);
    }

    /**
     * Add a listing to favorites
     */
    public function store(Request $request, Listing $listing)
    {
        // Check if already favorited
        $exists = Favorite::where('user_id', $request->user()->id)
            ->where('listing_id', $listing->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Listing already in favorites',
                'favorited' => true,
            ], 200);
        }

        // Create favorite
        $favorite = Favorite::create([
            'user_id' => $request->user()->id,
            'listing_id' => $listing->id,
        ]);

        return response()->json([
            'message' => 'Listing added to favorites',
            'favorite' => $favorite,
            'favorited' => true,
        ], 201);
    }

    /**
     * Remove a listing from favorites
     */
    public function destroy(Request $request, Listing $listing)
    {
        $deleted = Favorite::where('user_id', $request->user()->id)
            ->where('listing_id', $listing->id)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'message' => 'Listing not in favorites',
                'favorited' => false,
            ], 404);
        }

        return response()->json([
            'message' => 'Listing removed from favorites',
            'favorited' => false,
        ]);
    }

    /**
     * Check if a listing is favorited by the authenticated user
     */
    public function check(Request $request, Listing $listing)
    {
        $favorited = Favorite::where('user_id', $request->user()->id)
            ->where('listing_id', $listing->id)
            ->exists();

        return response()->json([
            'favorited' => $favorited,
        ]);
    }
}
