<?php

namespace App\Http\Controllers;

use App\Models\template;
use App\Rules\UniqueTemplateName;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class TemplateController extends Controller
{
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
        $request->validate([
            'name' => ['required', 'string', 'max:255', new UniqueTemplateName()],
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'html_content' => 'required|string',
            'css_variables' => 'nullable|json',
        ], [
            'name.required' => 'Nama template wajib diisi.',
            'name.max' => 'Nama template tidak boleh lebih dari 255 karakter.',
            'html_content.required' => 'Konten HTML template wajib diisi.',
            'cover_image.image' => 'File cover harus berupa gambar.',
            'cover_image.mimes' => 'Cover image harus berformat: jpg, jpeg, png, atau gif.',
            'cover_image.max' => 'Ukuran cover image tidak boleh lebih dari 2MB.',
        ]);

        $template = new Template();
        $template->name = $request->name;
        $template->slug = Str::slug($request->name);
        $template->description = $request->description;
        $template->html_content = $request->html_content;

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
           $coverImage = $request->file('cover_image');
           $fileName = time() . '_' . Str::slug($request->name) . '.' . $coverImage->getClientOriginalExtension();
           $coverImage->move(storage_path('app/public/template_covers'), $fileName);
           $template->cover_image = $fileName;
        }

        // Handle CSS variables
        if ($request->css_variables) {
            $template->css_variables = json_decode($request->css_variables, true);
        }

        $template->save();

        return redirect()->route('templates.index')->with('success', 'Template berhasil ditambahkan');
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
        $request->validate([
            'name' => ['required', 'string', 'max:255', new UniqueTemplateName($template->id)],
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'html_content' => 'required|string',
            'css_variables' => 'nullable|json',
        ], [
            'name.required' => 'Nama template wajib diisi.',
            'name.max' => 'Nama template tidak boleh lebih dari 255 karakter.',
            'html_content.required' => 'Konten HTML template wajib diisi.',
            'cover_image.image' => 'File cover harus berupa gambar.',
            'cover_image.mimes' => 'Cover image harus berformat: jpg, jpeg, png, atau gif.',
            'cover_image.max' => 'Ukuran cover image tidak boleh lebih dari 2MB.',
        ]);

        $template->name = $request->name;
        $template->slug = Str::slug($request->name);
        $template->description = $request->description;
        $template->html_content = $request->html_content;

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($template->cover_image) {
                Storage::delete('app/public/template_covers' . $template->cover_image);
            }

          $coverImage = $request->file('cover_image');
           $fileName = time() . '_' . Str::slug($request->name) . '.' . $coverImage->getClientOriginalExtension();
           $coverImage->move(storage_path('app/public/template_covers'), $fileName);
           $template->cover_image = $fileName;
        }

        // Handle CSS variables
        if ($request->css_variables) {
            $template->css_variables = json_decode($request->css_variables, true);
        }

        $template->save();

        return redirect()->route('templates.index')->with('success', 'Template berhasil diperbarui');
    }

    public function destroy(Template $template)
    {
        // Delete cover image
        if ($template->cover_image) {
            Storage::delete('public/template_covers/' . $template->cover_image);
        }

        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template berhasil dihapus');
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
}
