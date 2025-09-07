<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BroadcastController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware(function ($request, $next) {
    //         if (!Auth::user()->isAdmin()) {
    //             abort(403, 'Unauthorized access');
    //         }
    //         return $next($request);
    //     });
    // }

    /**
     * Display a listing of broadcasts.
     */
    public function index(Request $request)
    {
        $query = Broadcast::with('creator')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by title or message
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $broadcasts = $query->paginate(15);

        return view('admin.broadcasts.index', compact('broadcasts'));
    }

    /**
     * Show the form for creating a new broadcast.
     */
    public function create()
    {
        $users = User::where('role', '!=', 'admin')
                    ->where('is_banned', false)
                    ->orderBy('name')
                    ->get();

        return view('admin.broadcasts.create', compact('users'));
    }

    /**
     * Store a newly created broadcast.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'type' => ['required', Rule::in(['promo', 'update', 'maintenance', 'announcement'])],
            'target_type' => ['required', Rule::in(['all', 'specific'])],
            'target_users' => 'nullable|array',
            'target_users.*' => 'exists:users,id',
            'priority' => ['required', Rule::in([1, 2, 3])],
            'scheduled_at' => 'nullable|date|after:now',
            'send_now' => 'boolean'
        ]);

        // Validate target users for specific targeting
        if ($validated['target_type'] === 'specific' && empty($validated['target_users'])) {
            return back()->withErrors(['target_users' => 'Please select at least one user for specific targeting.']);
        }

        $broadcast = new Broadcast($validated);
        $broadcast->created_by = Auth::id();

        // Determine status
        if ($request->boolean('send_now')) {
            $broadcast->status = 'sent';
            $broadcast->sent_at = now();
        } elseif ($validated['scheduled_at']) {
            $broadcast->status = 'scheduled';
        } else {
            $broadcast->status = 'draft';
        }

        $broadcast->save();

        $message = $broadcast->status === 'sent' 
            ? 'Broadcast sent successfully!' 
            : 'Broadcast created successfully!';

        return redirect()->route('admin.broadcasts.index')->with('success', $message);
    }

    /**
     * Display the specified broadcast.
     */
    public function show(Broadcast $broadcast)
    {
        $broadcast->load(['creator', 'reads.user']);
        
        $targetUsers = $broadcast->getTargetUsers();
        $readCount = $broadcast->reads()->count();
        $totalTargets = $targetUsers->count();

        return view('admin.broadcasts.show', compact('broadcast', 'targetUsers', 'readCount', 'totalTargets'));
    }

    /**
     * Show the form for editing the broadcast.
     */
    public function edit(Broadcast $broadcast)
    {
        // Only allow editing drafts and scheduled broadcasts
        if (!in_array($broadcast->status, ['draft', 'scheduled'])) {
            return redirect()->route('admin.broadcasts.show', $broadcast)
                ->with('error', 'Cannot edit sent or cancelled broadcasts.');
        }

        $users = User::where('role', '!=', 'admin')
                    ->where('is_banned', false)
                    ->orderBy('name')
                    ->get();

        return view('admin.broadcasts.edit', compact('broadcast', 'users'));
    }

    /**
     * Update the specified broadcast.
     */
    public function update(Request $request, Broadcast $broadcast)
    {
        // Only allow updating drafts and scheduled broadcasts
        if (!in_array($broadcast->status, ['draft', 'scheduled'])) {
            return redirect()->route('admin.broadcasts.show', $broadcast)
                ->with('error', 'Cannot update sent or cancelled broadcasts.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'type' => ['required', Rule::in(['promo', 'update', 'maintenance', 'announcement'])],
            'target_type' => ['required', Rule::in(['all', 'specific'])],
            'target_users' => 'nullable|array',
            'target_users.*' => 'exists:users,id',
            'priority' => ['required', Rule::in([1, 2, 3])],
            'scheduled_at' => 'nullable|date|after:now',
            'send_now' => 'boolean'
        ]);

        // Validate target users for specific targeting
        if ($validated['target_type'] === 'specific' && empty($validated['target_users'])) {
            return back()->withErrors(['target_users' => 'Please select at least one user for specific targeting.']);
        }

        $broadcast->fill($validated);

        // Update status if needed
        if ($request->boolean('send_now')) {
            $broadcast->status = 'sent';
            $broadcast->sent_at = now();
        } elseif ($validated['scheduled_at']) {
            $broadcast->status = 'scheduled';
        } else {
            $broadcast->status = 'draft';
        }

        $broadcast->save();

        $message = $broadcast->status === 'sent' 
            ? 'Broadcast sent successfully!' 
            : 'Broadcast updated successfully!';

        return redirect()->route('admin.broadcasts.index')->with('success', $message);
    }

    /**
     * Send a scheduled or draft broadcast immediately.
     */
    public function send(Broadcast $broadcast)
    {
        if ($broadcast->status === 'sent') {
            return redirect()->back()->with('error', 'Broadcast has already been sent.');
        }

        $broadcast->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        return redirect()->back()->with('success', 'Broadcast sent successfully!');
    }

    /**
     * Cancel a scheduled broadcast.
     */
    public function cancel(Broadcast $broadcast)
    {
        if ($broadcast->status !== 'scheduled') {
            return redirect()->back()->with('error', 'Only scheduled broadcasts can be cancelled.');
        }

        $broadcast->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Broadcast cancelled successfully!');
    }

    /**
     * Remove the specified broadcast.
     */
    public function destroy(Broadcast $broadcast)
    {
        // Only allow deleting drafts and cancelled broadcasts
        if (in_array($broadcast->status, ['sent', 'scheduled'])) {
            return redirect()->back()->with('error', 'Cannot delete sent or scheduled broadcasts.');
        }

        $broadcast->delete();

        return redirect()->route('admin.broadcasts.index')->with('success', 'Broadcast deleted successfully!');
    }

    /**
     * Get broadcasts for user dashboard (API endpoint).
     */
    public function getUserBroadcasts(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return response()->json(['broadcasts' => []]);
        }

        $broadcasts = Broadcast::forUser($user)
            ->unreadByUser($user)
            ->orderBy('priority', 'desc')
            ->orderBy('sent_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'broadcasts' => $broadcasts->map(function ($broadcast) {
                return [
                    'id' => $broadcast->id,
                    'title' => $broadcast->title,
                    'message' => $broadcast->message,
                    'type' => $broadcast->type,
                    'priority' => $broadcast->priority,
                    'sent_at' => $broadcast->sent_at->format('Y-m-d H:i:s'),
                    'type_badge_color' => $broadcast->getTypeBadgeColor(),
                    'priority_badge_color' => $broadcast->getPriorityBadgeColor(),
                    'priority_text' => $broadcast->getPriorityText()
                ];
            })
        ]);
    }

    /**
     * Mark broadcast as read by user.
     */
    public function markAsRead(Request $request, Broadcast $broadcast)
    {
        $user = Auth::user();
        
        // Check if user is target of this broadcast
        $targetUsers = $broadcast->getTargetUsers();
        if (!$targetUsers->contains('id', $user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $broadcast->markAsReadBy($user);

        return response()->json(['success' => true]);
    }

    /**
     * Get analytics data for broadcasts.
     */
    public function analytics()
    {
        $totalBroadcasts = Broadcast::count();
        $sentBroadcasts = Broadcast::where('status', 'sent')->count();
        $scheduledBroadcasts = Broadcast::where('status', 'scheduled')->count();
        $draftBroadcasts = Broadcast::where('status', 'draft')->count();

        // Recent broadcasts with read rates
        $recentBroadcasts = Broadcast::with('reads')
            ->where('status', 'sent')
            ->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($broadcast) {
                $targetCount = $broadcast->getTargetUsers()->count();
                $readCount = $broadcast->reads()->count();
                $readRate = $targetCount > 0 ? ($readCount / $targetCount) * 100 : 0;

                return [
                    'id' => $broadcast->id,
                    'title' => $broadcast->title,
                    'type' => $broadcast->type,
                    'sent_at' => $broadcast->sent_at,
                    'target_count' => $targetCount,
                    'read_count' => $readCount,
                    'read_rate' => round($readRate, 2)
                ];
            });

        return view('admin.broadcasts.analytics', compact(
            'totalBroadcasts',
            'sentBroadcasts', 
            'scheduledBroadcasts',
            'draftBroadcasts',
            'recentBroadcasts'
        ));
    }
}
