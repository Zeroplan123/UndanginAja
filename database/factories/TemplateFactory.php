<?php

namespace Database\Factories;

use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Template>
 */
class TemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Template::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(10),
            'html_content' => $this->generateSampleHtmlContent(),
            'cover_image' => null,
            'css_variables' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate sample HTML content for testing
     */
    private function generateSampleHtmlContent(): string
    {
        return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Invitation</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .header { font-size: 24px; color: #8b4513; margin-bottom: 20px; }
        .couple { font-size: 32px; font-weight: bold; margin: 20px 0; }
        .details { font-size: 18px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="header">Wedding Invitation</div>
    <div class="couple">[bride_name] & [groom_name]</div>
    <div class="details">Date: [wedding_date]</div>
    <div class="details">Time: [wedding_time]</div>
    <div class="details">Venue: [venue]</div>
    <div class="details">Location: [location]</div>
    <div class="details">[additional_notes]</div>
</body>
</html>';
    }

    /**
     * Create a template with specific name
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
    }

    /**
     * Create a template with cover image
     */
    public function withCoverImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'cover_image' => 'sample_cover.jpg',
        ]);
    }

    /**
     * Create a template with CSS variables
     */
    public function withCssVariables(): static
    {
        return $this->state(fn (array $attributes) => [
            'css_variables' => [
                'primary_color' => '#8b4513',
                'secondary_color' => '#d4af37',
                'font_family' => 'Arial, sans-serif',
                'background_color' => '#ffffff',
            ],
        ]);
    }
}
