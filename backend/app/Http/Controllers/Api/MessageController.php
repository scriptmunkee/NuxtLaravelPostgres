<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Get all messages for the authenticated user
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Get all conversations (sent or received)
        $messages = Message::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->with(['sender', 'recipient', 'listing'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by conversation (other user + listing)
        $conversations = [];
        foreach ($messages as $message) {
            $otherUserId = $message->sender_id === $userId ? $message->recipient_id : $message->sender_id;
            $key = $otherUserId . '_' . $message->listing_id;

            if (!isset($conversations[$key])) {
                $conversations[$key] = [
                    'other_user' => $message->sender_id === $userId ? $message->recipient : $message->sender,
                    'listing' => $message->listing,
                    'messages' => [],
                    'last_message' => $message,
                    'unread_count' => 0,
                ];
            }

            $conversations[$key]['messages'][] = $message;

            // Count unread messages sent to current user
            if ($message->recipient_id === $userId && !$message->is_read) {
                $conversations[$key]['unread_count']++;
            }
        }

        return response()->json([
            'conversations' => array_values($conversations),
        ]);
    }

    /**
     * Get messages for a specific conversation
     */
    public function conversation(Request $request, Listing $listing, $userId)
    {
        $currentUserId = $request->user()->id;

        $messages = Message::where('listing_id', $listing->id)
            ->where(function ($query) use ($currentUserId, $userId) {
                $query->where(function ($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $currentUserId)
                      ->where('recipient_id', $userId);
                })->orWhere(function ($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $userId)
                      ->where('recipient_id', $currentUserId);
                });
            })
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('listing_id', $listing->id)
            ->where('sender_id', $userId)
            ->where('recipient_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required|exists:listings,id',
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Prevent sending messages to yourself
        if ($request->recipient_id == $request->user()->id) {
            return response()->json([
                'error' => 'You cannot send messages to yourself',
            ], 400);
        }

        $message = Message::create([
            'listing_id' => $request->listing_id,
            'sender_id' => $request->user()->id,
            'recipient_id' => $request->recipient_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $message->load(['sender', 'recipient', 'listing']);

        return response()->json([
            'message' => $message,
        ], 201);
    }

    /**
     * Mark a message as read
     */
    public function markAsRead(Request $request, Message $message)
    {
        // Only the recipient can mark a message as read
        if ($message->recipient_id !== $request->user()->id) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 403);
        }

        $message->update(['is_read' => true]);

        return response()->json([
            'message' => $message,
        ]);
    }
}
