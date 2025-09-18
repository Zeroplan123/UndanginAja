<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TemplateValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_template_names_on_create()
    {
        // Create existing template
        Template::factory()->create(['name' => 'Elegant Wedding']);

        // Try to create another template with same name
        $response = $this->actingAs($this->admin)
            ->post(route('templates.store'), [
                'name' => 'Elegant Wedding',
                'description' => 'Test description',
                'html_content' => '<h1>Test Template</h1>',
            ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertStringContainsString('sudah digunakan', session('errors')->first('name'));
    }

    /** @test */
    public function it_prevents_case_insensitive_duplicate_names()
    {
        // Create existing template
        Template::factory()->create(['name' => 'Elegant Wedding']);

        // Try to create template with different case
        $response = $this->actingAs($this->admin)
            ->post(route('templates.store'), [
                'name' => 'ELEGANT WEDDING',
                'description' => 'Test description',
                'html_content' => '<h1>Test Template</h1>',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function it_allows_unique_template_names()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('templates.store'), [
                'name' => 'Unique Template Name',
                'description' => 'Test description',
                'html_content' => '<h1>Test Template</h1>',
            ]);

        $response->assertRedirect(route('templates.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('templates', ['name' => 'Unique Template Name']);
    }

    /** @test */
    public function it_allows_same_name_when_updating_same_template()
    {
        $template = Template::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($this->admin)
            ->put(route('templates.update', $template), [
                'name' => 'Original Name', // Same name
                'description' => 'Updated description',
                'html_content' => '<h1>Updated Template</h1>',
            ]);

        $response->assertRedirect(route('templates.index'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function it_prevents_duplicate_names_when_updating_to_existing_name()
    {
        $template1 = Template::factory()->create(['name' => 'Template One']);
        $template2 = Template::factory()->create(['name' => 'Template Two']);

        $response = $this->actingAs($this->admin)
            ->put(route('templates.update', $template2), [
                'name' => 'Template One', // Try to use existing name
                'description' => 'Updated description',
                'html_content' => '<h1>Updated Template</h1>',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function ajax_check_name_returns_correct_availability()
    {
        // Create existing template
        Template::factory()->create(['name' => 'Existing Template']);

        // Check existing name
        $response = $this->actingAs($this->admin)
            ->postJson(route('templates.check-name'), [
                'name' => 'Existing Template'
            ]);

        $response->assertJson(['exists' => true]);

        // Check available name
        $response = $this->actingAs($this->admin)
            ->postJson(route('templates.check-name'), [
                'name' => 'Available Template'
            ]);

        $response->assertJson(['exists' => false]);
    }

    /** @test */
    public function ajax_check_name_ignores_current_template_on_edit()
    {
        $template = Template::factory()->create(['name' => 'Current Template']);

        // Check same name with ignore_id
        $response = $this->actingAs($this->admin)
            ->postJson(route('templates.check-name'), [
                'name' => 'Current Template',
                'ignore_id' => $template->id
            ]);

        $response->assertJson(['exists' => false]);
    }
}
