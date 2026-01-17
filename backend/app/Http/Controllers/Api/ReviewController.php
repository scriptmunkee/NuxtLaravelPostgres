<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Get reviews for a user (seller)
     */
    public function index(User $user)
    {
        $reviews = Review::where('reviewed_user_id', $user->id)
            ->with(['reviewer'])
            ->latest()
            ->paginate(10);

        $averageRating = Review::where('reviewed_user_id', $user->id)
            ->avg('rating');

        $ratingCounts = Review::where('reviewed_user_id', $user->id)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => round($averageRating, 1),
            'total_reviews' => $reviews->total(),
            'rating_counts' => $ratingCounts,
        ]);
    }

    /**
     * Submit a review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reviewed_user_id' => 'required|exists:users,id',
            'listing_id' => 'nullable|exists:listings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Prevent self-review
        if ($request->reviewed_user_id == $request->user()->id) {
            return response()->json([
                'error' => 'You cannot review yourself',
            ], 400);
        }

        // Check if already reviewed
        $existingReview = Review::where('reviewer_id', $request->user()->id)
            ->where('reviewed_user_id', $request->reviewed_user_id)
            ->where('listing_id', $request->listing_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'error' => 'You have already reviewed this seller for this listing',
            ], 400);
        }

        $review = Review::create([
            'reviewer_id' => $request->user()->id,
            'reviewed_user_id' => $request->reviewed_user_id,
            'listing_id' => $request->listing_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->load('reviewer');

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review,
        ], 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, Review $review)
    {
        // Only the reviewer can update their review
        if ($review->reviewer_id !== $request->user()->id) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review,
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, Review $review)
    {
        // Only the reviewer can delete their review
        if ($review->reviewer_id !== $request->user()->id) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }
}
