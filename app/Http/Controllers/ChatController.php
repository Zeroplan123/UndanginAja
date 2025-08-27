<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display user chat interface.
     */
    public function userIndex()
    {
        $user = Auth::user();
        
        // Get or create single conversation for user
        $conversation = $user->conversations()
            ->with(['messages.sender'])
            ->first();

        // Mark messages as read for user
        if ($conversation) {
            $conversation->markAsReadForUser();
        }

        return view('chat.user.index', compact('conversation'));
    }

    /**
     * Display admin chat management interface.
     */
    public function adminIndex()
    {
        $conversations = Conversation::with(['user', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
        ->orderBy('last_message_at', 'desc')
        ->paginate(20);

        $totalUnread = Message::where('sender_type', 'user')
            ->where('is_read', false)
            ->count();

        return view('admin.chat.index', compact('conversations', 'totalUnread'));
    }

    /**
     * Show specific conversation for user.
     */
    public function userShow(Conversation $conversation)
    {
        // Ensure user can only view their own conversations
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $conversation->load(['messages.sender', 'user']);
        $conversation->markAsReadForUser();

        return view('chat.user.show', compact('conversation'));
    }

    /**
     * Show specific conversation for admin.
     */
    public function adminShow(Conversation $conversation)
    {
        $conversation->load(['messages.sender', 'user']);
        $conversation->markAsReadForAdmin();

        return view('admin.chat.show', compact('conversation'));
    }

    /**
     * Create new conversation (user only).
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        
        // Check if user already has a conversation
        $existingConversation = $user->conversations()->first();
        if ($existingConversation) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki percakapan dengan admin.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'subject' => 'Chat dengan Admin',
                'status' => 'open',
                'last_message_at' => now()
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message' => $request->message,
                'sender_type' => 'user'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim!',
                'conversation_id' => $conversation->id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan.'
            ], 500);
        }
    }

    /**
     * Send message to existing conversation.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();
        
        // Check permissions
        if (!$user->isAdmin() && $conversation->user_id !== $user->id) {
            abort(403);
        }

        $senderType = $user->isAdmin() ? 'admin' : 'user';

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'sender_type' => $senderType
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        $message->load('sender');

        // Determine which partial to use based on context
        $partialView = 'chat.partials.message';
        if (request()->is('admin/*') || $user->isAdmin()) {
            $partialView = 'chat.partials.admin-message';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'html' => view($partialView, compact('message'))->render()
        ]);
    }

    /**
     * Get messages for a conversation (AJAX).
     */
    public function getMessages(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!$user->isAdmin() && $conversation->user_id !== $user->id) {
            abort(403);
        }

        $messages = $conversation->messages()->with('sender')->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Update conversation status (admin only).
     */
    public function updateStatus(Request $request, Conversation $conversation)
    {
        $request->validate([
            'status' => 'required|in:open,closed,pending'
        ]);

        $conversation->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status percakapan berhasil diperbarui!'
        ]);
    }

    /**
     * Get unread messages count.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $count = Message::where('sender_type', 'user')
                ->where('is_read', false)
                ->count();
        } else {
            $count = Message::whereHas('conversation', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();
        }

        return response()->json(['count' => $count]);
    }

    /**
     * Mark conversation as read.
     */
    public function markAsRead(Conversation $conversation)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $conversation->markAsReadForAdmin();
        } else {
            if ($conversation->user_id !== $user->id) {
                abort(403);
            }
            $conversation->markAsReadForUser();
        }

        return response()->json(['success' => true]);
    }
}
