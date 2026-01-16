<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with(['user', 'category', 'breed', 'location', 'primaryImage'])
            ->active();

        // Filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('breed_id')) {
            $query->where('breed_id', $request->breed_id);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->has('min_price') || $request->has('max_price')) {
            $query->priceRange($request->min_price, $request->max_price);
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'recent');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'recent':
            default:
                $query->recent();
                break;
        }

        $listings = $query->paginate($request->get('per_page', 12));

        return response()->json($listings);
    }

    public function show(Listing $listing)
    {
        $listing->load([
            'user.location',
            'category',
            'breed',
            'location',
            'images',
            'reviews.reviewer'
        ]);

        // Increment view count
        $listing->incrementViews();

        return response()->json([
            'listing' => $listing,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'breed_id' => 'nullable|exists:breeds,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'listing_type' => 'required|in:sale,adoption,stud',
            'price' => 'nullable|numeric|min:0',
            'age_years' => 'nullable|integer|min:0',
            'age_months' => 'nullable|integer|min:0|max:11',
            'gender' => 'required|in:male,female,unknown',
            'location_id' => 'required|exists:locations,id',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['status'] = 'draft';

        $listing = Listing::create($validated);

        return response()->json([
            'listing' => $listing->load(['category', 'breed', 'location']),
            'message' => 'Listing created successfully',
        ], 201);
    }

    public function update(Request $request, Listing $listing)
    {
        // Authorization check
        if ($listing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'breed_id' => 'nullable|exists:breeds,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'listing_type' => 'sometimes|in:sale,adoption,stud',
            'price' => 'nullable|numeric|min:0',
            'age_years' => 'nullable|integer|min:0',
            'age_months' => 'nullable|integer|min:0|max:11',
            'gender' => 'sometimes|in:male,female,unknown',
            'location_id' => 'sometimes|exists:locations,id',
            'status' => 'sometimes|in:draft,active,sold,inactive',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        }

        // If publishing for the first time
        if (isset($validated['status']) && $validated['status'] === 'active' && !$listing->published_at) {
            $validated['published_at'] = now();
        }

        $listing->update($validated);

        return response()->json([
            'listing' => $listing->load(['category', 'breed', 'location']),
            'message' => 'Listing updated successfully',
        ]);
    }

    public function destroy(Request $request, Listing $listing)
    {
        // Authorization check
        if ($listing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $listing->delete();

        return response()->json([
            'message' => 'Listing deleted successfully',
        ]);
    }

    public function myListings(Request $request)
    {
        $listings = Listing::with(['category', 'breed', 'location', 'primaryImage'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($listings);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $listings = Listing::with(['user', 'category', 'breed', 'location', 'primaryImage'])
            ->active()
            ->search($request->q)
            ->paginate(12);

        return response()->json($listings);
    }

    public function uploadImages(Request $request, Listing $listing)
    {
        // Authorization check
        if ($listing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        $images = [];
        $isFirst = $listing->images()->count() === 0;

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('listings/' . $listing->id, 'public');

            $listingImage = ListingImage::create([
                'listing_id' => $listing->id,
                'image_path' => $path,
                'display_order' => $index,
                'is_primary' => $isFirst && $index === 0,
            ]);

            $images[] = $listingImage;
        }

        return response()->json([
            'images' => $images,
            'message' => 'Images uploaded successfully',
        ]);
    }

    public function deleteImage(Request $request, ListingImage $image)
    {
        // Authorization check
        if ($image->listing->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete file from storage
        \Storage::disk('public')->delete($image->image_path);

        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully',
        ]);
    }
}
