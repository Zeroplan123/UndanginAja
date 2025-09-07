<?php

namespace App\Http\Controllers;

use App\Models\UserGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserGalleryController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('throttle:60,1')->only(['store']); // Rate limit uploads
    // }

    /**
     * Display user's gallery.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = UserGallery::forUser($user)->active()->orderBy('created_at', 'desc');

        // Search by caption or filename
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('caption', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        $galleryItems = $query->paginate(12);
        
        // Calculate storage usage
        $totalSize = UserGallery::forUser($user)->active()->sum('file_size');
        $maxSize = 100 * 1024 * 1024; // 100MB limit per user
        $usagePercentage = ($totalSize / $maxSize) * 100;

        return view('user.gallery.index', compact('galleryItems', 'totalSize', 'maxSize', 'usagePercentage'));
    }

    /**
     * Show the form for uploading new photos.
     */
    public function create()
    {
        $user = Auth::user();
        $totalSize = UserGallery::forUser($user)->active()->sum('file_size');
        $maxSize = 100 * 1024 * 1024; // 100MB limit
        
        if ($totalSize >= $maxSize) {
            return redirect()->route('user.gallery.index')
                ->with('error', 'Storage limit reached. Please delete some photos to upload new ones.');
        }

        return view('user.gallery.create');
    }

    /**
     * Store uploaded photos.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check storage limit
        $totalSize = UserGallery::forUser($user)->active()->sum('file_size');
        $maxSize = 100 * 1024 * 1024; // 100MB limit

        $request->validate([
            'photos' => 'required|array|max:10',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB per file
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:500'
        ]);

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('photos') as $index => $photo) {
            try {
                // Check if adding this file would exceed storage limit
                if (($totalSize + $photo->getSize()) > $maxSize) {
                    $errors[] = "File {$photo->getClientOriginalName()} would exceed storage limit.";
                    continue;
                }

                // Generate unique filename
                $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                $path = "galleries/{$user->id}";
                
                // Store original file
                $filePath = $photo->storeAs($path, $filename, 'public');
                
                // Get image dimensions and create thumbnail
                $fullPath = storage_path("app/public/{$filePath}");
                $imageInfo = getimagesize($fullPath);
                
                $metadata = [
                    'width' => $imageInfo[0] ?? null,
                    'height' => $imageInfo[1] ?? null,
                ];

                // Create thumbnail
                $this->createThumbnail($fullPath, $path, $filename);

                // Save to database
                $gallery = UserGallery::create([
                    'user_id' => $user->id,
                    'original_name' => $photo->getClientOriginalName(),
                    'file_name' => $filename,
                    'file_path' => $filePath,
                    'mime_type' => $photo->getMimeType(),
                    'file_size' => $photo->getSize(),
                    'caption' => $request->input("captions.{$index}"),
                    'metadata' => $metadata
                ]);

                $uploadedFiles[] = $gallery;
                $totalSize += $photo->getSize();

            } catch (\Exception $e) {
                $errors[] = "Failed to upload {$photo->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        $message = count($uploadedFiles) . ' photos uploaded successfully!';
        if (!empty($errors)) {
            $message .= ' Some files failed: ' . implode(', ', $errors);
        }

        return redirect()->route('user.gallery.index')->with('success', $message);
    }

    /**
     * Display the specified gallery item.
     */
    public function show(UserGallery $gallery)
    {
        // Ensure user can only view their own gallery items
        if ($gallery->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('user.gallery.show', compact('gallery'));
    }

    /**
     * Show the form for editing the gallery item.
     */
    public function edit(UserGallery $gallery)
    {
        // Ensure user can only edit their own gallery items
        if ($gallery->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('user.gallery.edit', compact('gallery'));
    }

    /**
     * Update the gallery item caption.
     */
    public function update(Request $request, UserGallery $gallery)
    {
        // Ensure user can only update their own gallery items
        if ($gallery->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'caption' => 'nullable|string|max:500'
        ]);

        $gallery->update([
            'caption' => $request->caption
        ]);

        return redirect()->route('user.gallery.index')->with('success', 'Caption updated successfully!');
    }

    /**
     * Remove the gallery item.
     */
    public function destroy(UserGallery $gallery)
    {
        // Ensure user can only delete their own gallery items
        if ($gallery->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Delete files from storage
        if (Storage::disk('public')->exists($gallery->file_path)) {
            Storage::disk('public')->delete($gallery->file_path);
        }

        // Delete thumbnail if exists
        $thumbnailPath = str_replace('/galleries/', '/galleries/thumbnails/', $gallery->file_path);
        if (Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('public')->delete($thumbnailPath);
        }

        $gallery->delete();

        return redirect()->route('user.gallery.index')->with('success', 'Photo deleted successfully!');
    }

    /**
     * Bulk delete gallery items.
     */
    public function bulkDelete(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'gallery_ids' => 'required|array',
            'gallery_ids.*' => 'exists:user_galleries,id'
        ]);

        $galleries = UserGallery::whereIn('id', $request->gallery_ids)
            ->where('user_id', $user->id)
            ->get();

        $deletedCount = 0;
        foreach ($galleries as $gallery) {
            // Delete files from storage
            if (Storage::disk('public')->exists($gallery->file_path)) {
                Storage::disk('public')->delete($gallery->file_path);
            }

            // Delete thumbnail if exists
            $thumbnailPath = str_replace('/galleries/', '/galleries/thumbnails/', $gallery->file_path);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            $gallery->delete();
            $deletedCount++;
        }

        return redirect()->route('user.gallery.index')
            ->with('success', "{$deletedCount} photos deleted successfully!");
    }

    /**
     * Download gallery item.
     */
    public function download(UserGallery $gallery)
    {
        // Ensure user can only download their own gallery items
        if ($gallery->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        if (!Storage::disk('public')->exists($gallery->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($gallery->file_path, $gallery->original_name);
    }

    /**
     * Get gallery statistics.
     */
    public function statistics()
    {
        $user = Auth::user();
        
        $totalPhotos = UserGallery::forUser($user)->active()->count();
        $totalSize = UserGallery::forUser($user)->active()->sum('file_size');
        $maxSize = 100 * 1024 * 1024; // 100MB
        
        $recentPhotos = UserGallery::forUser($user)
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Group by month for chart
        $monthlyStats = UserGallery::forUser($user)
            ->active()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(file_size) as size')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'total_photos' => $totalPhotos,
            'total_size' => $totalSize,
            'max_size' => $maxSize,
            'usage_percentage' => ($totalSize / $maxSize) * 100,
            'recent_photos' => $recentPhotos,
            'monthly_stats' => $monthlyStats
        ]);
    }

    /**
     * Create thumbnail for uploaded image.
     */
    private function createThumbnail($originalPath, $storagePath, $filename)
    {
        try {
            $thumbnailPath = storage_path("app/public/{$storagePath}/thumbnails");
            
            // Create thumbnails directory if it doesn't exist
            if (!file_exists($thumbnailPath)) {
                mkdir($thumbnailPath, 0755, true);
            }

            // Create thumbnail using Intervention Image (if available)
            if (class_exists('Intervention\Image\Facades\Image')) {
                $thumbnail = Image::make($originalPath)
                    ->fit(300, 300, function ($constraint) {
                        $constraint->upsize();
                    });
                
                $thumbnail->save($thumbnailPath . '/' . $filename, 80);
            } else {
                // Fallback: copy original as thumbnail
                copy($originalPath, $thumbnailPath . '/' . $filename);
            }
        } catch (\Exception $e) {
            // Thumbnail creation failed, but don't fail the upload
            \Log::warning("Thumbnail creation failed: " . $e->getMessage());
        }
    }
}
