<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Simple Elegant',
                'slug' => 'simple-elegant',
                'description' => 'Template undangan simple dan elegant dengan gradasi biru-ungu yang modern.',
                'preview_image' => null,
                'file_path' => 'templates/simple-elegant.html',
                'cover_image' => null,
                'html_content' => file_get_contents(public_path('templates/simple-elegant.html')),
                'css_variables' => [
                    'primary_color' => '#667eea',
                    'secondary_color' => '#764ba2',
                    'background_color' => '#f5f7fa'
                ]
            ],
            [
                'name' => 'Romantic Pink',
                'slug' => 'romantic-pink',
                'description' => 'Template undangan romantis dengan nuansa pink yang lembut dan ornamen bunga.',
                'preview_image' => null,
                'file_path' => 'templates/romantic-pink.html',
                'cover_image' => null,
                'html_content' => file_get_contents(public_path('templates/romantic-pink.html')),
                'css_variables' => [
                    'primary_color' => '#db7093',
                    'secondary_color' => '#8b4a6b',
                    'background_color' => '#ffeef8'
                ]
            ],
            [
                'name' => 'Modern Blue',
                'slug' => 'modern-blue',
                'description' => 'Template undangan modern dengan gradasi biru yang elegan dan desain minimalis.',
                'preview_image' => null,
                'file_path' => 'templates/modern-blue.html',
                'cover_image' => null,
                'html_content' => file_get_contents(public_path('templates/modern-blue.html')),
                'css_variables' => [
                    'primary_color' => '#667eea',
                    'secondary_color' => '#764ba2',
                    'background_color' => '#f8f9ff'
                ]
            ],
            [
                'name' => 'Classic Green',
                'slug' => 'classic-green',
                'description' => 'Template undangan klasik dengan nuansa hijau natural dan border tradisional.',
                'preview_image' => null,
                'file_path' => 'templates/classic-green.html',
                'cover_image' => null,
                'html_content' => file_get_contents(public_path('templates/classic-green.html')),
                'css_variables' => [
                    'primary_color' => '#7fb069',
                    'secondary_color' => '#2d4a2b',
                    'background_color' => '#a8e6cf'
                ]
            ]
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }
    }
}
