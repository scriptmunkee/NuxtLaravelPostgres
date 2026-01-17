<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * Get all reports (admin only - placeholder)
     */
    public function index()
    {
        // TODO: Add admin middleware
        $reports = Report::with(['user', 'listing'])
            ->latest()
            ->paginate(20);

        return response()->json($reports);
    }

    /**
     * Submit a report for a listing
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required|exists:listings,id',
            'reason' => 'required|string|in:spam,scam,inappropriate,misleading,other',
            'description' => 'required|string|min:10|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if user already reported this listing
        $existingReport = Report::where('user_id', $request->user()->id)
            ->where('listing_id', $request->listing_id)
            ->first();

        if ($existingReport) {
            return response()->json([
                'error' => 'You have already reported this listing',
            ], 400);
        }

        $report = Report::create([
            'user_id' => $request->user()->id,
            'listing_id' => $request->listing_id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // If multiple reports, flag the listing
        $reportCount = Report::where('listing_id', $request->listing_id)->count();
        if ($reportCount >= 3) {
            $listing = Listing::find($request->listing_id);
            if ($listing && $listing->status !== 'flagged') {
                $listing->update(['status' => 'flagged']);
            }
        }

        return response()->json([
            'message' => 'Report submitted successfully. Our team will review it.',
            'report' => $report,
        ], 201);
    }

    /**
     * Update report status (admin only - placeholder)
     */
    public function update(Request $request, Report $report)
    {
        // TODO: Add admin middleware
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,reviewed,resolved,dismissed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $report->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Report status updated',
            'report' => $report,
        ]);
    }
}
