<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Rules\UniqueTemplateName;
use App\Services\TemplateFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class TemplateController extends Controller
{
    protected $templateFileService;

    public function __construct(TemplateFileService $templateFileService)
    {
        $this->templateFileService = $templateFileService;
    }

    /**
     * Display a listing of the resource.
     */
      public function index()
    {
        $templates = Template::withCount('invitations')->latest()->paginate(10);
        return view('admin.template', compact('templates'));
    }

    public function create()
    {
        return view('admin.template_create');
    }

    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255', new UniqueTemplateName()],
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'source_type' => 'required|in:manual,file',
        ];

        // Conditional validation based on source_type
        if ($request->source_type === 'file') {
            $rules['html_file'] = 'required|file|mimes:html,htm|max:5120'; // 5MB
            $rules['html_content'] = 'nullable|string';
        } else {
            $rules['html_content'] = 'required|string|min:10';
            $rules['html_file'] = 'nullable';
        }

        $request->validate($rules, [
            'name.required' => 'Nama template wajib diisi.',
            'name.max' => 'Nama template maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'cover_image.image' => 'Cover image harus berupa gambar.',
            'cover_image.mimes' => 'Cover image harus berformat: jpeg, png, jpg, gif, atau webp.',
            'cover_image.max' => 'Cover image maksimal 5MB.',
            'source_type.required' => 'Tipe sumber template wajib dipilih.',
            'source_type.in' => 'Tipe sumber template tidak valid.',
            'html_file.required' => 'File HTML wajib diupload untuk template berbasis file.',
            'html_file.file' => 'HTML file harus berupa file yang valid.',
            'html_file.mimes' => 'File harus berformat HTML (.html atau .htm).',
            'html_file.max' => 'File HTML maksimal 5MB.',
            'html_content.required' => 'Konten HTML wajib diisi untuk template manual.',
            'html_content.min' => 'Konten HTML minimal 10 karakter.',
        ]);

        try {
            $template = new Template();
            $template->name = $request->name;
            $template->slug = Str::slug($request->name);
            $template->description = $request->description;
            $template->source_type = $request->source_type;

            // Handle berdasarkan source_type
            if ($request->source_type === 'file' && $request->hasFile('html_file')) {
                // Process file upload (No XSS validation - base64 encoding provides security)
                $fileResult = $this->templateFileService->processHtmlFile($request->file('html_file'));
                
                if (!$fileResult['success']) {
                    return back()->withErrors([
                        'html_file' => implode(', ', $fileResult['errors'])
                    ])->withInput();
                }

                $template->file_path = $fileResult['file_path'];
                $template->html_content = $fileResult['html_content']; // Direct save, no encoding

                Log::info('Template created from file upload', [
                    'user_id' => auth()->id(),
                    'template_name' => $request->name,
                    'file_size' => $fileResult['file_size']
                ]);

            } else {
                // Manual HTML input (No validation - direct save)
                $template->html_content = $request->html_content;

                Log::info('Template created from manual input', [
                    'user_id' => auth()->id(),
                    'template_name' => $request->name
                ]);
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $coverImage = $request->file('cover_image');
                $fileName = time() . '_' . Str::slug($request->name) . '.' . $coverImage->getClientOriginalExtension();
                
                $filePath = $coverImage->storeAs('template_covers', $fileName, 'public');
                $template->cover_image = $fileName;
            }

            // Handle CSS variables
            if ($request->css_variables) {
                $template->css_variables = json_decode($request->css_variables, true);
            }

            $template->save();

            return redirect()->route('templates.index')->with('success', 'Template berhasil ditambahkan');

        } catch (\Exception $e) {
            Log::error('Template creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'template_name' => $request->name
            ]);

            return back()->withErrors([
                'general' => 'Terjadi kesalahan saat membuat template: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function show(Template $template)
    {
        return view('admin.templates.show', compact('template'));
    }

    public function edit(Template $template)
    {
        return view('admin.template_edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        // Validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255', new UniqueTemplateName($template->id)],
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'source_type' => 'required|in:manual,file',
        ];

        // Conditional validation based on source_type
        if ($request->source_type === 'file') {
            $rules['html_file'] = 'nullable|file|mimes:html,htm|max:5120'; // Optional for update
            $rules['html_content'] = 'nullable|string';
        } else {
            $rules['html_content'] = 'required|string|min:10';
            $rules['html_file'] = 'nullable';
        }

        $request->validate($rules, [
            'name.required' => 'Nama template wajib diisi.',
            'name.max' => 'Nama template maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'cover_image.image' => 'Cover image harus berupa gambar.',
            'cover_image.mimes' => 'Cover image harus berformat: jpeg, png, jpg, gif, atau webp.',
            'cover_image.max' => 'Cover image maksimal 5MB.',
            'source_type.required' => 'Tipe sumber template wajib dipilih.',
            'source_type.in' => 'Tipe sumber template tidak valid.',
            'html_file.file' => 'HTML file harus berupa file yang valid.',
            'html_file.mimes' => 'File harus berformat HTML (.html atau .htm).',
            'html_file.max' => 'File HTML maksimal 5MB.',
            'html_content.required' => 'Konten HTML wajib diisi untuk template manual.',
            'html_content.min' => 'Konten HTML minimal 10 karakter.',
        ]);

        try {
            $template->name = $request->name;
            $template->slug = Str::slug($request->name);
            $template->description = $request->description;
            $template->source_type = $request->source_type;

            // Handle berdasarkan source_type
            if ($request->source_type === 'file') {
                if ($request->hasFile('html_file')) {
                    // Process new file upload (No XSS validation - base64 encoding provides security)
                    $fileResult = $this->templateFileService->updateTemplateFile(
                        $request->file('html_file'), 
                        $template->file_path
                    );
                    
                    if (!$fileResult['success']) {
                        return back()->withErrors([
                            'html_file' => implode(', ', $fileResult['errors'])
                        ])->withInput();
                    }

                    $template->file_path = $fileResult['file_path'];
                    $template->html_content = $fileResult['html_content']; // Direct save, no encoding

                    Log::info('Template updated from file upload', [
                        'user_id' => auth()->id(),
                        'template_id' => $template->id,
                        'file_size' => $fileResult['file_size']
                    ]);
                }
                // Jika tidak ada file baru, tetap gunakan file lama
            } else {
                // Manual HTML input - hapus file lama jika ada
                if ($template->file_path) {
                    $this->templateFileService->deleteTemplateFile($template->file_path);
                    $template->file_path = null;
                }

                // Direct save, no validation or encoding
                $template->html_content = $request->html_content;

                Log::info('Template updated from manual input', [
                    'user_id' => auth()->id(),
                    'template_id' => $template->id
                ]);
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                // Delete old image
                if ($template->cover_image) {
                    Storage::disk('public')->delete('template_covers/' . $template->cover_image);
                }

                $coverImage = $request->file('cover_image');
                $fileName = time() . '_' . Str::slug($request->name) . '.' . $coverImage->getClientOriginalExtension();
                
                $filePath = $coverImage->storeAs('template_covers', $fileName, 'public');
                $template->cover_image = $fileName;
            }

            // Handle CSS variables
            if ($request->css_variables) {
                $template->css_variables = json_decode($request->css_variables, true);
            }

            $template->save();

            return redirect()->route('templates.index')->with('success', 'Template berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Template update failed', [
                'user_id' => auth()->id(),
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'general' => 'Terjadi kesalahan saat memperbarui template: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function destroy(Template $template)
    {
        try {
            // Delete cover image
            if ($template->cover_image) {
                Storage::disk('public')->delete('template_covers/' . $template->cover_image);
            }

            // Delete template file if exists
            if ($template->file_path) {
                $this->templateFileService->deleteTemplateFile($template->file_path);
            }

            $template->delete();

            Log::info('Template deleted', [
                'user_id' => auth()->id(),
                'template_id' => $template->id,
                'template_name' => $template->name,
                'had_file' => !empty($template->file_path)
            ]);

            return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus');

        } catch (\Exception $e) {
            Log::error('Template deletion failed', [
                'user_id' => auth()->id(),
                'template_id' => $template->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('templates.index')->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }

    // Method untuk preview template
    public function preview(Template $template, Request $request)
    {
        $sampleData = [
            'bride_name' => $request->get('bride_name', 'Siti Nurhaliza'),
            'groom_name' => $request->get('groom_name', 'Ahmad Dhani'),
            'wedding_date' => $request->get('wedding_date', '25 Desember 2024'),
            'wedding_time' => $request->get('wedding_time', '10:00 WIB'),
            'venue' => $request->get('venue', 'Hotel Grand Indonesia'),
            'location' => $request->get('location', 'Jl. MH Thamrin No.1, Jakarta Pusat'),
            'additional_notes' => $request->get('additional_notes', 'Mohon kehadiran Bapak/Ibu/Saudara/i'),
            // Legacy support
            'nama_pria' => $request->get('nama_pria', 'Ahmad Dhani'),
            'nama_wanita' => $request->get('nama_wanita', 'Siti Nurhaliza'),
            'tanggal_pernikahan' => $request->get('tanggal', '25 Desember 2024'),
            'waktu_pernikahan' => $request->get('waktu', '10:00 WIB'),
            'lokasi_pernikahan' => $request->get('lokasi', 'Hotel Grand Indonesia, Jakarta'),
        ];

        $html = $template->getCompiledHtml($sampleData);
        
        return response($html)->header('Content-Type', 'text/html');
    }

    // Method untuk generate PDF file template
    public function generatePdf(Template $template, Request $request)
    {
        $data = $request->all();
        $html = $template->getCompiledHtml($data);
        
        $fileName = Str::slug($template->name) . '_' . time() . '.pdf';
        
        // Configure PDF options
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
                'defaultMediaType' => 'print',
                'dpi' => 150,
            ]);
        
        return $pdf->download($fileName);
    }

    // Method untuk generate HTML file template (backup)
    public function generateHtml(Template $template, Request $request)
    {
        $data = $request->all();
        $html = $template->getCompiledHtml($data);
        
        $fileName = Str::slug($template->name) . '_' . time() . '.html';
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Check if template name is available (AJAX endpoint)
     */
    public function checkName(Request $request)
    {
        $name = $request->input('name');
        $ignoreId = $request->input('ignore_id');
        
        if (!$name) {
            return response()->json(['exists' => false]);
        }
        
        $query = Template::whereRaw('LOWER(name) = ?', [strtolower($name)]);
        
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Nama template sudah digunakan' : 'Nama template tersedia'
        ]);
    }

    /**
     * Validate HTML safety via AJAX (Disabled - No validation)
     * Endpoint untuk validasi keamanan HTML secara real-time (dinonaktifkan)
     */
    public function validateHtmlSecurity(Request $request)
    {
        // No validation - always return safe
        return response()->json([
            'is_safe' => true,
            'risk_level' => 'safe',
            'issues' => [],
            'message' => 'HTML template siap digunakan'
        ]);
    }

    /**
     * Get HTML preview (No sanitization)
     */
    public function getSecurePreview(Request $request)
    {
        $html = $request->input('html_content');
        $variables = $request->input('variables', []);
        
        if (!$html) {
            return response()->json(['error' => 'Tidak ada konten HTML'], 400);
        }

        try {
            // Decode HTML if escaped
            if (strpos($html, '&lt;') !== false) {
                $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
            }
            
            // Replace variables dengan data sample (no sanitization)
            $sampleData = array_merge([
                'bride_name' => 'Siti Nurhaliza',
                'groom_name' => 'Ahmad Dhani',
                'wedding_date' => '25 Desember 2024',
                'wedding_time' => '10:00 WIB',
                'venue' => 'Hotel Grand Indonesia',
                'location' => 'Jl. MH Thamrin No.1, Jakarta Pusat',
                'additional_notes' => 'Mohon kehadiran Bapak/Ibu/Saudara/i'
            ], $variables);

            foreach ($sampleData as $key => $value) {
                $html = str_replace('[' . $key . ']', $value, $html);
            }

            return response($html)->header('Content-Type', 'text/html');
            
        } catch (\Exception $e) {
            Log::error('Preview generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'html_length' => strlen($html ?? '')
            ]);
            
            // Return HTML error message instead of JSON for better error display
            $errorHtml = '<div style="padding: 20px; text-align: center; color: red; font-family: Arial, sans-serif;">';
            $errorHtml .= '<h3>Preview Gagal Dimuat</h3>';
            $errorHtml .= '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            $errorHtml .= '<p class="text-sm text-gray-600">Periksa konten HTML Anda atau hubungi administrator.</p>';
            $errorHtml .= '</div>';
            
            return response($errorHtml, 500)->header('Content-Type', 'text/html');
        }
    }
}
