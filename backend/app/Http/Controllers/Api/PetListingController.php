<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PetListing;
use Illuminate\Http\Request;

class PetListingController extends Controller
{
    public function index(Request $request)
    {
        $query = PetListing::with('user')->where('is_active', true);

        if ($request->has('breed')) {
            $query->where('breed', 'like', '%' . $request->breed . '%');
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json($listings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'breed' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'age_months' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'gender' => 'nullable|string|in:male,female',
            'images' => 'nullable|array',
        ]);

        $listing = $request->user()->petListings()->create($validated);

        return response()->json($listing, 201);
    }

    public function show(PetListing $petListing)
    {
        $petListing->load('user');
        return response()->json($petListing);
    }

    public function update(Request $request, PetListing $petListing)
    {
        if ($petListing->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'breed' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:255',
            'age_months' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'gender' => 'nullable|string|in:male,female',
            'images' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $petListing->update($validated);

        return response()->json($petListing);
    }

    public function destroy(PetListing $petListing, Request $request)
    {
        if ($petListing->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $petListing->delete();

        return response()->json(['message' => 'Listing deleted successfully']);
    }

    public function myListings(Request $request)
    {
        $listings = $request->user()->petListings()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($listings);
    }
}
