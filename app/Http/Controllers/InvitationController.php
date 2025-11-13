<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Template;
use App\Services\HtmlToImageService;
use App\Services\ImageToPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

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





    /**
     * Debug template content for troubleshooting
     */
    public function debugTemplate($id)
    {
        try {
            $invitation = Invitation::with('template')->findOrFail($id);
            
            // Check if user owns this invitation
            if ($invitation->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to invitation');
            }

            $debug = [
                'invitation_id' => $invitation->id,
                'template_id' => $invitation->template->id,
                'template_name' => $invitation->template->name,
                'file_path' => $invitation->template->file_path,
                'file_path_exists' => false,
                'html_content_length' => 0,
                'template_files_available' => []
            ];

            // Check file_path
            if (!empty($invitation->template->file_path)) {
                $templatePath = public_path('templates/' . $invitation->template->file_path);
                $debug['file_path_full'] = $templatePath;
                $debug['file_path_exists'] = file_exists($templatePath);
            }

            // Check html_content
            if (!empty($invitation->template->html_content)) {
                $debug['html_content_length'] = strlen($invitation->template->html_content);
                $debug['html_content_preview'] = substr($invitation->template->html_content, 0, 200) . '...';
            }

            // List available template files
            $templateDir = public_path('templates');
            if (is_dir($templateDir)) {
                $files = glob($templateDir . '/*.html');
                $debug['template_files_available'] = array_map('basename', $files);
            }

            return response()->json($debug);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }



    /**
     * Export invitation to PDF via image conversion (HTML -> Image -> PDF)
     */
    public function exportPdfViaImage($id)
    {
        try {
            $invitation = Invitation::with('template')->findOrFail($id);
            
            // Check if user owns this invitation
            if ($invitation->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to invitation');
            }

            // Prepare variables for template compilation
            $variables = [
                'bride_name' => $invitation->bride_name,
                'groom_name' => $invitation->groom_name,
                'wedding_date' => $invitation->wedding_date->format('d F Y'),
                'wedding_time' => $invitation->wedding_time ?? '08:00 WIB',
                'venue' => $invitation->venue ?? $invitation->location,
                'location' => $invitation->location ?? $invitation->venue,
                'additional_notes' => $invitation->additional_notes ?? '',
            ];

            // Get compiled HTML using template method with forPdf=true
            $htmlContent = $invitation->template->getCompiledHtml($variables, true);
            
            // Check if content is valid
            if (empty($htmlContent) || strpos($htmlContent, 'Template content tidak dapat dimuat') !== false) {
                throw new Exception("Template content not found for template: " . $invitation->template->name);
            }

            // Generate filename
            $filename = 'undangan-' . Str::slug($invitation->bride_name . '-' . $invitation->groom_name);
            
            // Use ImageToPdfService to convert HTML -> Image -> PDF
            $imageToPdfService = new ImageToPdfService(new HtmlToImageService());
            $pdfFile = $imageToPdfService->convertHtmlToPdfViaImage($htmlContent, $filename);
            
            // Return download response
            $downloadName = $filename . '.pdf';
            return $imageToPdfService->getDownloadResponse($pdfFile, $downloadName);

        } catch (Exception $e) {
            Log::error('PDF export via image failed', [
                'invitation_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // LibreOffice fallback removed

            // Final fallback to standard PDF if enabled
            if (config('image_pdf.fallback.fallback_to_dompdf', true)) {
                try {
                    Log::info('Attempting final fallback to standard PDF export', ['invitation_id' => $id]);
                    return redirect()->route('user.export-pdf', $invitation->slug);
                } catch (Exception $finalError) {
                    Log::error('All PDF export methods failed', [
                        'invitation_id' => $id,
                        'final_error' => $finalError->getMessage()
                    ]);
                }
            }

            // Provide helpful error message with solutions
            $errorMessage = 'Gagal mengexport PDF via image: ' . $e->getMessage();
            
            if (strpos($e->getMessage(), 'GD extension') !== false) {
                $errorMessage .= ' Silakan install PHP GD extension atau gunakan metode export lainnya.';
            } elseif (strpos($e->getMessage(), 'No image conversion methods') !== false) {
                $errorMessage .= ' Silakan install Chrome browser atau wkhtmltoimage.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'solutions' => [
                    'Install PHP GD extension untuk image processing',
                    'Install Google Chrome browser untuk konversi HTML ke gambar',
                    'Install wkhtmltoimage dari https://wkhtmltopdf.org/',
                    'Gunakan opsi "Standard PDF" untuk template sederhana'
                ]
            ], 500);
        }
    }

    /**
     * Test HTML to Image conversion capabilities
     */
    public function testImageConversion()
    {
        try {
            $htmlToImageService = new HtmlToImageService();
            $result = $htmlToImageService->testConversion();
            
            return response()->json([
                'success' => true,
                'message' => 'Image conversion test completed',
                'results' => $result,
                'recommendations' => [
                    'For best results, install one of the following:',
                    '1. Chrome/Chromium browser (recommended)',
                    '2. wkhtmltoimage from https://wkhtmltopdf.org/downloads.html',
                    '3. Browsershot with Node.js and Puppeteer'
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image conversion test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test complete HTML -> Image -> PDF workflow
     */
    public function testCompleteImageToPdf()
    {
        try {
            $imageToPdfService = new ImageToPdfService(new HtmlToImageService());
            $result = $imageToPdfService->testConversion();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Complete image to PDF test successful' : 'Test failed',
                'results' => $result
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Complete image to PDF test failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
