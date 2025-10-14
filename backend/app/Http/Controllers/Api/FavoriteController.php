<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\PetListing;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favorites()
            ->with('petListing.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($favorites);
    }

    public function toggle(Request $request, PetListing $petListing)
    {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('pet_listing_id', $petListing->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Removed from favorites', 'is_favorited' => false]);
        } else {
            Favorite::create([
                'user_id' => $request->user()->id,
                'pet_listing_id' => $petListing->id,
            ]);
            return response()->json(['message' => 'Added to favorites', 'is_favorited' => true]);
        }
    }
}
