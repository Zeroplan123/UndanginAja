<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil invitations milik user yang sedang login
        $userInvitations = Invitation::with('template')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        // Jika Anda ingin menampilkan templates juga di halaman history
        $templates = Template::all();

        return view('user.history', compact('userInvitations', 'templates'));
    }

    public function dashboard()
    {
        // Untuk dashboard utama, ambil beberapa invitations terbaru
        $userInvitations = Invitation::with('template')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(3)
            ->get();

        $templates = Template::all();

        return view('dashboard', compact('userInvitations', 'templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = Template::all();
        return view('invitations.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
            'bride_name' => 'required|string|max:255',
            'groom_name' => 'required|string|max:255',
            'wedding_date' => 'required|date',
            'wedding_time' => 'nullable|string|max:255',
            'venue' => 'required|string|max:255',
            'location' => 'nullable|string|max:500',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        $slug = Str::slug($request->bride_name . '-' . $request->groom_name . '-' . now()->format('Y-m-d'));
        
        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (Invitation::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $invitation = Invitation::create([
            'user_id' => Auth::id(),
            'template_id' => $request->template_id,
            'bride_name' => $request->bride_name,
            'groom_name' => $request->groom_name,
            'wedding_date' => $request->wedding_date,
            'wedding_time' => $request->wedding_time,
            'venue' => $request->venue,
            'location' => $request->location,
            'additional_notes' => $request->additional_notes,
            'slug' => $slug,
        ]);

        return redirect()->route('invitations.index')
            ->with('success', 'Undangan berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invitation $invitation)
    {
        // Check if user owns this invitation
        if ($invitation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('invitations.show', compact('invitation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invitation $invitation)
    {
        // Check if user owns this invitation
        if ($invitation->user_id !== Auth::id()) {
            abort(403);
        }

        $templates = Template::all();
        return view('invitations.edit', compact('invitation', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invitation $invitation)
    {
        // Check if user owns this invitation
        if ($invitation->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'template_id' => 'required|exists:templates,id',
            'bride_name' => 'required|string|max:255',
            'groom_name' => 'required|string|max:255',
            'wedding_date' => 'required|date',
            'wedding_time' => 'nullable|string|max:255',
            'venue' => 'required|string|max:255',
            'location' => 'nullable|string|max:500',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        // Update slug if names changed
        if ($request->bride_name !== $invitation->bride_name || $request->groom_name !== $invitation->groom_name) {
            $slug = Str::slug($request->bride_name . '-' . $request->groom_name . '-' . now()->format('Y-m-d'));
            
            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (Invitation::where('slug', $slug)->where('id', '!=', $invitation->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $invitation->slug = $slug;
        }

        $invitation->update([
            'template_id' => $request->template_id,
            'bride_name' => $request->bride_name,
            'groom_name' => $request->groom_name,
            'wedding_date' => $request->wedding_date,
            'wedding_time' => $request->wedding_time,
            'venue' => $request->venue,
            'location' => $request->location,
            'additional_notes' => $request->additional_notes,
            'slug' => $invitation->slug,
        ]);

        return redirect()->route('invitations.index')
            ->with('success', 'Undangan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invitation $invitation)
    {
        // Check if user owns this invitation
        if ($invitation->user_id !== Auth::id()) {
            abort(403);
        }

        $invitation->delete();

        return redirect()->route('invitations.index')
            ->with('success', 'Undangan berhasil dihapus!');
    }

    /**
     * Preview the invitation with template
     */
    public function preview(Invitation $invitation)
    {
        // Check if user owns this invitation
        if ($invitation->user_id !== Auth::id()) {
            abort(403);
        }

        $invitation->load('template');
        
        // Prepare variables for template compilation
        $variables = [
            'bride_name' => $invitation->bride_name,
            'groom_name' => $invitation->groom_name,
            'wedding_date' => $invitation->wedding_date->format('d F Y'),
            'wedding_time' => $invitation->wedding_time ?? '08:00 WIB',
            'venue' => $invitation->venue ?? $invitation->location,
            'location' => $invitation->location ?? $invitation->venue,
        ];

        return view('invitations.preview', compact('invitation', 'variables'));
    }
}
