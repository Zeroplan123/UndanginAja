<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class UserGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'caption',
        'metadata',
        'is_active'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'file_size' => 'integer'
    ];

    /**
     * Get the user who owns this gallery item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active gallery items.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific user's gallery.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Get file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get image dimensions if available.
     */
    public function getDimensions(): ?array
    {
        if (!$this->isImage() || !$this->metadata) {
            return null;
        }

        return [
            'width' => $this->metadata['width'] ?? null,
            'height' => $this->metadata['height'] ?? null
        ];
    }

    /**
     * Delete file from storage when model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($gallery) {
            if (Storage::exists($gallery->file_path)) {
                Storage::delete($gallery->file_path);
            }
        });
    }

    /**
     * Get thumbnail URL (if exists).
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->isImage()) {
            return null;
        }

        $thumbnailPath = str_replace('/galleries/', '/galleries/thumbnails/', $this->file_path);
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::disk('public')->url($thumbnailPath);
        }

        return $this->file_url;
    }

    /**
     * Soft delete by setting is_active to false.
     */
    public function softDelete(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Restore soft deleted item.
     */
    public function restore(): bool
    {
        return $this->update(['is_active' => true]);
    }
}
