<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use App\Services\HtmlSanitizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Test suite untuk validasi keamanan template HTML
 * Memastikan sistem dapat mencegah stored XSS attacks
 */
class TemplateSecurityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user untuk testing
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);
        
        $this->sanitizer = new HtmlSanitizerService();
    }

    /** @test */
    public function test_html_escaping_for_storage()
    {
        $maliciousHtml = '<script>alert("XSS Attack!")</script><div>Normal content</div>';
        
        $escaped = $this->sanitizer->escapeForStorage($maliciousHtml);
        
        // Pastikan script tag di-escape
        $this->assertStringContains('&lt;script&gt;', $escaped);
        $this->assertStringNotContains('<script>', $escaped);
        
        // Pastikan konten normal juga di-escape
        $this->assertStringContains('&lt;div&gt;', $escaped);
    }

    /** @test */
    public function test_html_sanitization_for_output()
    {
        $escapedMaliciousHtml = '&lt;script&gt;alert("XSS")&lt;/script&gt;&lt;div&gt;Safe content&lt;/div&gt;';
        
        $sanitized = $this->sanitizer->sanitizeForOutput($escapedMaliciousHtml);
        
        // Script tag harus dihapus setelah sanitasi
        $this->assertStringNotContains('<script>', $sanitized);
        $this->assertStringNotContains('alert(', $sanitized);
        
        // Konten aman harus tetap ada
        $this->assertStringContains('<div>Safe content</div>', $sanitized);
    }

    /** @test */
    public function test_security_validation_detects_dangerous_content()
    {
        $dangerousHtml = '
            <script>document.location="http://evil.com"</script>
            <div onclick="alert(\'XSS\')">Click me</div>
            <iframe src="javascript:alert(\'XSS\')"></iframe>
        ';
        
        $report = $this->sanitizer->validateHtmlSafety($dangerousHtml);
        
        $this->assertFalse($report['is_safe']);
        $this->assertEquals('high', $report['risk_level']);
        $this->assertNotEmpty($report['issues']);
    }

    /** @test */
    public function test_security_validation_allows_safe_content()
    {
        $safeHtml = '
            <div class="invitation">
                <h1>Wedding Invitation</h1>
                <p>Join us for our special day</p>
                <img src="photo.jpg" alt="Wedding photo">
            </div>
        ';
        
        $report = $this->sanitizer->validateHtmlSafety($safeHtml);
        
        $this->assertTrue($report['is_safe']);
        $this->assertEquals('none', $report['risk_level']);
        $this->assertEmpty($report['issues']);
    }

    /** @test */
    public function test_template_creation_blocks_high_risk_content()
    {
        $this->actingAs($this->admin);
        
        $maliciousTemplate = [
            'name' => 'Malicious Template',
            'description' => 'Test template',
            'html_content' => '<script>alert("XSS Attack!")</script><div>Normal content</div>',
            'css_variables' => '{"color": "red"}'
        ];
        
        $response = $this->post(route('templates.store'), $maliciousTemplate);
        
        // Harus ada error karena konten berbahaya
        $response->assertSessionHasErrors(['html_content']);
        
        // Template tidak boleh tersimpan
        $this->assertDatabaseMissing('templates', [
            'name' => 'Malicious Template'
        ]);
    }

    /** @test */
    public function test_template_creation_allows_safe_content()
    {
        $this->actingAs($this->admin);
        
        $safeTemplate = [
            'name' => 'Safe Template',
            'description' => 'Safe wedding template',
            'html_content' => '<div class="invitation"><h1>[bride_name] & [groom_name]</h1><p>Wedding Date: [wedding_date]</p></div>',
            'css_variables' => '{"primary_color": "#667eea"}'
        ];
        
        $response = $this->post(route('templates.store'), $safeTemplate);
        
        $response->assertRedirect(route('templates.index'));
        $response->assertSessionHas('success');
        
        // Template harus tersimpan dengan HTML yang di-escape
        $this->assertDatabaseHas('templates', [
            'name' => 'Safe Template'
        ]);
        
        $template = Template::where('name', 'Safe Template')->first();
        
        // HTML harus di-escape di database
        $this->assertStringContains('&lt;div', $template->html_content);
    }

    /** @test */
    public function test_template_compiled_html_is_safe()
    {
        // Buat template dengan HTML yang sudah di-escape
        $template = Template::create([
            'name' => 'Test Template',
            'slug' => 'test-template',
            'html_content' => '&lt;div&gt;&lt;h1&gt;[bride_name] &amp; [groom_name]&lt;/h1&gt;&lt;script&gt;alert("XSS")&lt;/script&gt;&lt;/div&gt;'
        ]);
        
        $variables = [
            'bride_name' => 'Jane',
            'groom_name' => 'John'
        ];
        
        $compiledHtml = $template->getCompiledHtml($variables);
        
        // Script tag harus dihapus
        $this->assertStringNotContains('<script>', $compiledHtml);
        $this->assertStringNotContains('alert(', $compiledHtml);
        
        // Variabel harus ter-replace dengan aman
        $this->assertStringContains('Jane &amp; John', $compiledHtml);
        
        // HTML struktur harus tetap ada
        $this->assertStringContains('<div>', $compiledHtml);
        $this->assertStringContains('<h1>', $compiledHtml);
    }

    /** @test */
    public function test_xss_through_template_variables()
    {
        $template = Template::create([
            'name' => 'Variable Test Template',
            'slug' => 'variable-test',
            'html_content' => '&lt;div&gt;&lt;h1&gt;[bride_name]&lt;/h1&gt;&lt;/div&gt;'
        ]);
        
        // Coba inject XSS melalui variabel
        $maliciousVariables = [
            'bride_name' => '<script>alert("XSS via variable")</script>Jane'
        ];
        
        $compiledHtml = $template->getCompiledHtml($maliciousVariables);
        
        // Script dalam variabel harus di-escape
        $this->assertStringNotContains('<script>', $compiledHtml);
        $this->assertStringContains('&lt;script&gt;', $compiledHtml);
        $this->assertStringContains('Jane', $compiledHtml);
    }

    /** @test */
    public function test_security_validation_ajax_endpoint()
    {
        $this->actingAs($this->admin);
        
        $maliciousHtml = '<script>alert("XSS")</script><div>Content</div>';
        
        $response = $this->postJson(route('templates.validate-security'), [
            'html_content' => $maliciousHtml
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'is_safe' => false,
            'risk_level' => 'high'
        ]);
        
        $this->assertStringContains('script', $response->json('message'));
    }

    /** @test */
    public function test_secure_preview_endpoint()
    {
        $this->actingAs($this->admin);
        
        $htmlWithXSS = '<div><h1>Title</h1><script>alert("XSS")</script></div>';
        
        $response = $this->postJson(route('templates.secure-preview'), [
            'html_content' => $htmlWithXSS
        ]);
        
        $response->assertStatus(200);
        
        $previewHtml = $response->getContent();
        
        // Script harus dihapus dari preview
        $this->assertStringNotContains('<script>', $previewHtml);
        $this->assertStringNotContains('alert(', $previewHtml);
        
        // Konten aman harus tetap ada
        $this->assertStringContains('<div>', $previewHtml);
        $this->assertStringContains('<h1>Title</h1>', $previewHtml);
    }

    /** @test */
    public function test_event_handler_removal()
    {
        $htmlWithEvents = '
            <div onclick="alert(\'XSS\')" onmouseover="steal_data()">
                <p onload="malicious_code()">Content</p>
                <a href="#" onclick="return false;">Link</a>
            </div>
        ';
        
        $sanitized = $this->sanitizer->sanitizeForOutput(
            $this->sanitizer->escapeForStorage($htmlWithEvents)
        );
        
        // Event handlers harus dihapus
        $this->assertStringNotContains('onclick=', $sanitized);
        $this->assertStringNotContains('onmouseover=', $sanitized);
        $this->assertStringNotContains('onload=', $sanitized);
        
        // Struktur HTML harus tetap ada
        $this->assertStringContains('<div>', $sanitized);
        $this->assertStringContains('<p>', $sanitized);
        $this->assertStringContains('<a href="#">', $sanitized);
    }

    /** @test */
    public function test_javascript_url_removal()
    {
        $htmlWithJsUrls = '
            <a href="javascript:alert(\'XSS\')">Link 1</a>
            <img src="javascript:void(0)" onerror="alert(\'XSS\')">
            <iframe src="javascript:alert(\'XSS\')"></iframe>
        ';
        
        $sanitized = $this->sanitizer->sanitizeForOutput(
            $this->sanitizer->escapeForStorage($htmlWithJsUrls)
        );
        
        // JavaScript URLs harus dihapus
        $this->assertStringNotContains('javascript:', $sanitized);
        
        // Tag iframe harus dihapus (dangerous tag)
        $this->assertStringNotContains('<iframe', $sanitized);
    }

    /** @test */
    public function test_css_expression_removal()
    {
        $htmlWithCssExpression = '
            <div style="width: expression(alert(\'XSS\')); color: red;">
                <p style="background: url(javascript:alert(\'XSS\'));">Content</p>
            </div>
        ';
        
        $sanitized = $this->sanitizer->sanitizeForOutput(
            $this->sanitizer->escapeForStorage($htmlWithCssExpression)
        );
        
        // CSS expression harus dihapus
        $this->assertStringNotContains('expression(', $sanitized);
        $this->assertStringNotContains('javascript:', $sanitized);
        
        // CSS aman harus tetap ada
        $this->assertStringContains('color: red', $sanitized);
    }

    /** @test */
    public function test_template_model_mutator_escapes_html()
    {
        $template = new Template();
        $template->name = 'Test Template';
        $template->html_content = '<script>alert("XSS")</script><div>Content</div>';
        
        // Mutator harus escape HTML otomatis
        $this->assertStringContains('&lt;script&gt;', $template->html_content);
        $this->assertStringNotContains('<script>', $template->html_content);
    }

    /** @test */
    public function test_security_report_generation()
    {
        $complexMaliciousHtml = '
            <script>alert("XSS")</script>
            <div onclick="steal_cookies()">Click me</div>
            <iframe src="http://evil.com"></iframe>
            <object data="malicious.swf"></object>
            <p>Normal content</p>
        ';
        
        $report = $this->sanitizer->generateSecurityReport($complexMaliciousHtml);
        
        $this->assertArrayHasKey('is_safe', $report);
        $this->assertArrayHasKey('risk_level', $report);
        $this->assertArrayHasKey('issues_found', $report);
        $this->assertArrayHasKey('script_tags', $report);
        $this->assertArrayHasKey('event_handlers', $report);
        
        $this->assertFalse($report['is_safe']);
        $this->assertEquals('high', $report['risk_level']);
        $this->assertGreaterThan(0, $report['script_tags']);
        $this->assertGreaterThan(0, $report['event_handlers']);
    }

    /** @test */
    public function test_clean_for_preview_function()
    {
        $htmlWithMixedContent = '
            <div>
                <h1>Safe Title</h1>
                <script>alert("XSS")</script>
                <p onclick="malicious()">Paragraph</p>
                <a href="javascript:void(0)">Link</a>
                <span>Safe content</span>
            </div>
        ';
        
        $cleaned = $this->sanitizer->cleanForPreview($htmlWithMixedContent);
        
        // Konten berbahaya harus dihapus
        $this->assertStringNotContains('<script>', $cleaned);
        $this->assertStringNotContains('onclick=', $cleaned);
        $this->assertStringNotContains('javascript:', $cleaned);
        
        // Konten aman harus tetap ada
        $this->assertStringContains('<h1>Safe Title</h1>', $cleaned);
        $this->assertStringContains('<span>Safe content</span>', $cleaned);
    }
}
