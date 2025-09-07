<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserGallery;

class GalleryUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && !auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'photos' => [
                'required',
                'array',
                'max:10' // Maximum 10 files at once
            ],
            'photos.*' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:10240', // 10MB max per file
                'dimensions:min_width=100,min_height=100,max_width=8000,max_height=8000'
            ],
            'captions' => [
                'nullable',
                'array'
            ],
            'captions.*' => [
                'nullable',
                'string',
                'max:500'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'Please select at least one photo to upload.',
            'photos.array' => 'Invalid file format.',
            'photos.max' => 'You can upload maximum 10 photos at once.',
            'photos.*.required' => 'Each file must be a valid image.',
            'photos.*.image' => 'Only image files are allowed.',
            'photos.*.mimes' => 'Supported formats: JPEG, PNG, JPG, GIF, WebP.',
            'photos.*.max' => 'Each image must be smaller than 10MB.',
            'photos.*.dimensions' => 'Image dimensions must be between 100x100 and 8000x8000 pixels.',
            'captions.*.max' => 'Caption cannot exceed 500 characters.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            
            // Check storage limit
            $currentUsage = UserGallery::forUser($user)->active()->sum('file_size');
            $maxStorage = 100 * 1024 * 1024; // 100MB limit
            
            if ($currentUsage >= $maxStorage) {
                $validator->errors()->add('photos', 'Storage limit reached. Please delete some photos first.');
                return;
            }
            
            // Calculate total size of uploaded files
            $uploadSize = 0;
            if ($this->hasFile('photos')) {
                foreach ($this->file('photos') as $file) {
                    $uploadSize += $file->getSize();
                }
            }
            
            if (($currentUsage + $uploadSize) > $maxStorage) {
                $remainingSpace = $maxStorage - $currentUsage;
                $remainingMB = round($remainingSpace / 1024 / 1024, 2);
                $validator->errors()->add('photos', "Upload would exceed storage limit. You have {$remainingMB}MB remaining.");
            }
            
            // Rate limiting - max 50 uploads per hour
            $recentUploads = UserGallery::forUser($user)
                ->where('created_at', '>=', now()->subHour())
                ->count();
                
            if ($recentUploads >= 50) {
                $validator->errors()->add('photos', 'Upload limit reached. You can upload maximum 50 photos per hour.');
            }
            
            // Check for duplicate files (by name and size)
            if ($this->hasFile('photos')) {
                foreach ($this->file('photos') as $file) {
                    $existingFile = UserGallery::forUser($user)
                        ->active()
                        ->where('original_name', $file->getClientOriginalName())
                        ->where('file_size', $file->getSize())
                        ->first();
                        
                    if ($existingFile) {
                        $validator->errors()->add('photos', "File '{$file->getClientOriginalName()}' appears to be a duplicate.");
                        break;
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Sanitize captions
        if ($this->has('captions')) {
            $captions = $this->input('captions', []);
            $sanitizedCaptions = [];
            
            foreach ($captions as $key => $caption) {
                if (!empty($caption)) {
                    // Remove potentially harmful content
                    $sanitizedCaptions[$key] = strip_tags(trim($caption));
                }
            }
            
            $this->merge(['captions' => $sanitizedCaptions]);
        }
    }
}
