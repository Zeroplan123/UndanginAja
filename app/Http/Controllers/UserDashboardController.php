<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Template;
use App\Services\PdfExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class UserDashboardController extends Controller
{
    /**
     * Show the history page with user's invitations and available templates
     */
    public function index() 
    {
        // Ambil invitations milik user yang sedang login
        $userInvitations = Invitation::with('template')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        
        // Ambil semua templates yang tersedia
        $templates = Template::all();
        
        return view('user.history', compact('userInvitations', 'templates'));
    }

    /**
     * Show the main dashboard
     */
    public function dashboard()
    {
        $templates = Template::latest()->get();
        $userInvitations = auth()->user()->invitations()
            ->with('template')
            ->latest()
            ->take(3) // Hanya ambil 3 terbaru untuk dashboard
            ->get();
        
        return view('user.dashboard', compact('templates', 'userInvitations'));
    }

    /**
     * Show form to create invitation with selected template
     */
    public function createInvitation(Template $template)
    {
        return view('user.create_invitation', compact('template'));
    }

    /**
     * Store new invitation
     */
    public function storeInvitation(Request $request, Template $template)
    {
        $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'wedding_date' => 'required|date',
            'wedding_time' => 'required|string',
            'venue' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'additional_notes' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $invitation = new Invitation();
        $invitation->user_id = auth()->id();
        $invitation->template_id = $template->id;
        $invitation->groom_name = $request->groom_name;
        $invitation->bride_name = $request->bride_name;
        $invitation->wedding_date = $request->wedding_date;
        $invitation->wedding_time = $request->wedding_time;
        $invitation->venue = $request->venue;
        $invitation->location = $request->location;
        $invitation->additional_notes = $request->additional_notes;
        $invitation->slug = Str::slug($request->groom_name . '-' . $request->bride_name . '-' . time());

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $fileName = time() . '_' . Str::slug($request->groom_name . '_' . $request->bride_name) . '.' . $coverImage->getClientOriginalExtension();
            $coverImage->move(storage_path('app/public/invitation_covers'), $fileName);
            $invitation->cover_image = $fileName;
        }

        $invitation->save();

        return redirect()->route('user.invitation.preview', $invitation->slug)
                        ->with('success', 'Undangan berhasil dibuat!');
    }

    /**
     * Preview invitation
     */
    public function previewInvitation($slug)
    {
        $invitation = Invitation::where('slug', $slug)
                               ->where('user_id', auth()->id())
                               ->with('template')
                               ->firstOrFail();

        $templateData = [
            'bride_name' => $invitation->bride_name,
            'groom_name' => $invitation->groom_name,
            'wedding_date' => date('d F Y', strtotime($invitation->wedding_date)),
            'wedding_time' => $invitation->wedding_time,
            'venue' => $invitation->venue ?? $invitation->location,
            'location' => $invitation->location,
            'additional_notes' => $invitation->additional_notes,
            // Legacy support
            'nama_mempelai_pria' => $invitation->groom_name,
            'nama_mempelai_wanita' => $invitation->bride_name,
            'tanggal_pernikahan' => date('d F Y', strtotime($invitation->wedding_date)),
            'waktu_pernikahan' => $invitation->wedding_time,
            'lokasi_pernikahan' => $invitation->location,
            'catatan_tambahan' => $invitation->additional_notes,
        ];

        $compiledHtml = $invitation->template->getCompiledHtml($templateData);

        return view('user.preview_invitation', compact('invitation', 'compiledHtml'));
    }

    /**
     * Export invitation as PDF with enhanced compatibility
     */
    public function exportPDF($slug, PdfExportService $pdfService)
    {
        $invitation = Invitation::where('slug', $slug)
                               ->where('user_id', auth()->id())
                               ->with('template')
                               ->firstOrFail();

        $templateData = [
            'bride_name' => $invitation->bride_name,
            'groom_name' => $invitation->groom_name,
            'wedding_date' => date('d F Y', strtotime($invitation->wedding_date)),
            'wedding_time' => $invitation->wedding_time,
            'venue' => $invitation->venue ?? $invitation->location,
            'location' => $invitation->location,
            'additional_notes' => $invitation->additional_notes,
            'bride_father' => $invitation->bride_father,
            'bride_mother' => $invitation->bride_mother,
            'groom_father' => $invitation->groom_father,
            'groom_mother' => $invitation->groom_mother,
            // Legacy support
            'nama_mempelai_pria' => $invitation->groom_name,
            'nama_mempelai_wanita' => $invitation->bride_name,
            'tanggal_pernikahan' => date('d F Y', strtotime($invitation->wedding_date)),
            'waktu_pernikahan' => $invitation->wedding_time,
            'lokasi_pernikahan' => $invitation->location,
            'catatan_tambahan' => $invitation->additional_notes,
        ];

        try {
            return $pdfService->generateForDownload($invitation, $templateData);
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage(), [
                'invitation_id' => $invitation->id,
                'slug' => $slug,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Gagal mengexport PDF. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Show user's invitations (Legacy method - untuk backward compatibility)
     */
    public function myInvitations()
    {
        $invitations = auth()->user()->invitations()
            ->with('template')
            ->latest()
            ->paginate(6);
            
        return view('user.my_invitations', compact('invitations'));
    }

    /**
     * Delete invitation (Legacy method - untuk backward compatibility)
     */
    public function deleteInvitation($slug)
    {
        $invitation = Invitation::where('slug', $slug)
                               ->where('user_id', auth()->id())
                               ->firstOrFail();

        // Delete cover image if exists
        if ($invitation->cover_image) {
            Storage::delete('public/invitation_covers/' . $invitation->cover_image);
        }

        $invitation->delete();

        return redirect()->route('user.history')->with('success', 'Undangan berhasil dihapus!');
    }

    /**
     * Preview template
     */
    public function preview($id)
    {
        // ambil template berdasarkan id
        $template = Template::findOrFail($id);

        return view('user.preview', compact('template'));
    }
}