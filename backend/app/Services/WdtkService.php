<?php

namespace App\Services;

use App\Models\FoiAuthority;
use App\Models\FoiRequest;
use App\Models\FoiStatusUpdate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WdtkService
{
    private const BASE_URL = 'https://www.whatdotheyknow.com';
    private const TIMEOUT = 30;

    /**
     * Fetch JSON data for a specific FOI request from WhatDoTheyKnow.
     */
    public function fetchRequest(string $urlTitle): ?array
    {
        $url = self::BASE_URL . "/request/{$urlTitle}.json";

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("WDTK: Failed to fetch request {$urlTitle}", [
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("WDTK: Error fetching request {$urlTitle}", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch JSON data for an authority from WhatDoTheyKnow.
     */
    public function fetchAuthority(string $slug): ?array
    {
        $url = self::BASE_URL . "/body/{$slug}.json";

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error("WDTK: Error fetching authority {$slug}", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Search FOI requests on WhatDoTheyKnow.
     */
    public function searchRequests(string $query, int $page = 1): ?array
    {
        $url = self::BASE_URL . '/search/' . urlencode($query) . '/all.json';

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->acceptJson()
                ->get($url, ['page' => $page]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error("WDTK: Error searching requests", [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch the Atom feed for a user's requests.
     */
    public function fetchUserFeed(string $username): ?array
    {
        $url = self::BASE_URL . "/feed/user/{$username}.json";

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->acceptJson()
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error("WDTK: Error fetching user feed {$username}", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Import a request by its URL title (slug) and start tracking it.
     */
    public function importRequest(string $urlTitle): ?FoiRequest
    {
        $data = $this->fetchRequest($urlTitle);

        if (!$data) {
            return null;
        }

        $authority = $this->syncAuthority($data);

        return FoiRequest::updateOrCreate(
            ['wdtk_url_title' => $urlTitle],
            [
                'title' => $data['title'] ?? $urlTitle,
                'foi_authority_id' => $authority?->id,
                'authority_name' => $data['public_body']['name'] ?? null,
                'status' => $data['described_state'] ?? 'unknown',
                'display_status' => $data['display_status'] ?? null,
                'description' => $data['initial_request_text'] ?? null,
                'wdtk_url' => $data['request_url'] ?? self::BASE_URL . "/request/{$urlTitle}",
                'requester_name' => $data['user_name'] ?? null,
                'request_created_at' => isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at']) : null,
                'request_updated_at' => isset($data['updated_at']) ? \Carbon\Carbon::parse($data['updated_at']) : null,
                'is_tracked' => true,
                'raw_json' => $data,
                'last_polled_at' => now(),
            ]
        );
    }

    /**
     * Poll a tracked request for status changes.
     * Returns the status update record if status changed, null otherwise.
     */
    public function pollRequest(FoiRequest $foiRequest): ?FoiStatusUpdate
    {
        $data = $this->fetchRequest($foiRequest->wdtk_url_title);

        if (!$data) {
            $foiRequest->update(['last_polled_at' => now()]);
            return null;
        }

        $newStatus = $data['described_state'] ?? 'unknown';
        $oldStatus = $foiRequest->status;

        $foiRequest->update([
            'title' => $data['title'] ?? $foiRequest->title,
            'status' => $newStatus,
            'display_status' => $data['display_status'] ?? null,
            'request_updated_at' => isset($data['updated_at']) ? \Carbon\Carbon::parse($data['updated_at']) : null,
            'raw_json' => $data,
            'last_polled_at' => now(),
        ]);

        if ($newStatus !== $oldStatus) {
            $statusUpdate = FoiStatusUpdate::create([
                'foi_request_id' => $foiRequest->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'display_status' => $data['display_status'] ?? null,
                'description' => "Status changed from '{$oldStatus}' to '{$newStatus}'",
                'detected_at' => now(),
            ]);

            Log::info("WDTK: Status change detected for '{$foiRequest->title}'", [
                'request_id' => $foiRequest->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

            return $statusUpdate;
        }

        return null;
    }

    /**
     * Poll all tracked requests and return any status changes.
     *
     * @return FoiStatusUpdate[]
     */
    public function pollAllTracked(): array
    {
        $trackedRequests = FoiRequest::tracked()->get();
        $updates = [];

        foreach ($trackedRequests as $request) {
            $update = $this->pollRequest($request);

            if ($update) {
                $updates[] = $update;
            }

            // Be respectful to the server - small delay between requests
            usleep(500_000); // 0.5 seconds
        }

        return $updates;
    }

    /**
     * Sync authority data from a request's JSON response.
     */
    private function syncAuthority(array $requestData): ?FoiAuthority
    {
        $publicBody = $requestData['public_body'] ?? null;

        if (!$publicBody || empty($publicBody['url_name'])) {
            return null;
        }

        return FoiAuthority::updateOrCreate(
            ['wdtk_slug' => $publicBody['url_name']],
            [
                'name' => $publicBody['name'] ?? $publicBody['url_name'],
                'short_name' => $publicBody['short_name'] ?? null,
                'url' => $publicBody['home_page'] ?? null,
                'last_synced_at' => now(),
            ]
        );
    }

    /**
     * Get a summary of all known statuses across tracked requests.
     */
    public function getStatusSummary(): array
    {
        return FoiRequest::tracked()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
