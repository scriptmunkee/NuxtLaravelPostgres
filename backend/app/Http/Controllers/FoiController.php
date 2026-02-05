<?php

namespace App\Http\Controllers;

use App\Models\FoiRequest;
use App\Models\FoiStatusUpdate;
use App\Services\WdtkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoiController extends Controller
{
    public function __construct(
        private WdtkService $wdtk,
    ) {}

    /**
     * List all tracked FOI requests with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FoiRequest::with('authority');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->boolean('tracked_only', true)) {
            $query->tracked();
        }

        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('authority_name', 'ilike', "%{$search}%");
            });
        }

        $requests = $query->orderByDesc('request_updated_at')
            ->paginate($request->integer('per_page', 25));

        return response()->json($requests);
    }

    /**
     * Get a single FOI request with its status history.
     */
    public function show(FoiRequest $foiRequest): JsonResponse
    {
        $foiRequest->load(['authority', 'statusUpdates']);

        return response()->json($foiRequest);
    }

    /**
     * Import a new request from WhatDoTheyKnow by URL slug.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'url_title' => 'required|string|max:500',
        ]);

        $urlTitle = $request->input('url_title');

        // Extract slug from full URL if provided
        if (str_contains($urlTitle, 'whatdotheyknow.com/request/')) {
            $urlTitle = basename(parse_url($urlTitle, PHP_URL_PATH));
        }

        $foiRequest = $this->wdtk->importRequest($urlTitle);

        if (!$foiRequest) {
            return response()->json([
                'message' => 'Failed to import request. Check the URL or slug is correct.',
            ], 422);
        }

        return response()->json($foiRequest->load('authority'), 201);
    }

    /**
     * Manually trigger a poll for a specific request.
     */
    public function poll(FoiRequest $foiRequest): JsonResponse
    {
        $update = $this->wdtk->pollRequest($foiRequest);

        return response()->json([
            'request' => $foiRequest->fresh()->load('authority'),
            'status_changed' => $update !== null,
            'update' => $update,
        ]);
    }

    /**
     * Update tracking settings or internal notes for a request.
     */
    public function update(Request $request, FoiRequest $foiRequest): JsonResponse
    {
        $validated = $request->validate([
            'is_tracked' => 'sometimes|boolean',
            'internal_notes' => 'sometimes|nullable|string|max:10000',
            'tags' => 'sometimes|nullable|array',
            'tags.*' => 'string|max:100',
        ]);

        $foiRequest->update($validated);

        return response()->json($foiRequest->fresh()->load('authority'));
    }

    /**
     * Delete (stop tracking) a request.
     */
    public function destroy(FoiRequest $foiRequest): JsonResponse
    {
        $foiRequest->delete();

        return response()->json(null, 204);
    }

    /**
     * Get all status updates, optionally filtered.
     */
    public function statusUpdates(Request $request): JsonResponse
    {
        $query = FoiStatusUpdate::with('foiRequest');

        if ($request->boolean('unacknowledged_only')) {
            $query->unacknowledged();
        }

        if ($request->has('foi_request_id')) {
            $query->where('foi_request_id', $request->integer('foi_request_id'));
        }

        $updates = $query->orderByDesc('detected_at')
            ->paginate($request->integer('per_page', 50));

        return response()->json($updates);
    }

    /**
     * Acknowledge a status update (mark as reviewed).
     */
    public function acknowledgeUpdate(FoiStatusUpdate $foiStatusUpdate): JsonResponse
    {
        $foiStatusUpdate->update(['is_acknowledged' => true]);

        return response()->json($foiStatusUpdate);
    }

    /**
     * Acknowledge all unacknowledged updates.
     */
    public function acknowledgeAll(): JsonResponse
    {
        $count = FoiStatusUpdate::unacknowledged()->update(['is_acknowledged' => true]);

        return response()->json(['acknowledged' => $count]);
    }

    /**
     * Get a dashboard summary.
     */
    public function dashboard(): JsonResponse
    {
        $summary = $this->wdtk->getStatusSummary();
        $unacknowledgedCount = FoiStatusUpdate::unacknowledged()->count();
        $recentUpdates = FoiStatusUpdate::with('foiRequest')
            ->orderByDesc('detected_at')
            ->limit(10)
            ->get();
        $overdueRequests = FoiRequest::tracked()->overdue()->with('authority')->get();
        $awaitingAction = FoiRequest::tracked()->awaitingAction()->with('authority')->get();
        $totalTracked = FoiRequest::tracked()->count();

        return response()->json([
            'total_tracked' => $totalTracked,
            'status_summary' => $summary,
            'unacknowledged_updates' => $unacknowledgedCount,
            'recent_updates' => $recentUpdates,
            'overdue_requests' => $overdueRequests,
            'awaiting_action' => $awaitingAction,
        ]);
    }
}
