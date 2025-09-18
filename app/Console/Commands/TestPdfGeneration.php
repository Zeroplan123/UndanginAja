<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use App\Services\PdfExportService;
use Illuminate\Console\Command;

class TestPdfGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:test {invitation_id? : ID of invitation to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PDF generation with various templates and settings';

    /**
     * Execute the console command.
     */
    public function handle(PdfExportService $pdfService)
    {
        $invitationId = $this->argument('invitation_id');
        
        if ($invitationId) {
            $invitation = Invitation::with('template')->find($invitationId);
            if (!$invitation) {
                $this->error("Invitation with ID {$invitationId} not found.");
                return 1;
            }
            $invitations = collect([$invitation]);
        } else {
            $invitations = Invitation::with('template')->take(3)->get();
            if ($invitations->isEmpty()) {
                $this->error("No invitations found in database.");
                return 1;
            }
        }

        $this->info("Testing PDF generation...");
        $this->newLine();

        foreach ($invitations as $invitation) {
            $this->info("Testing invitation: {$invitation->groom_name} & {$invitation->bride_name}");
            $this->info("Template: {$invitation->template->name}");
            
            try {
                $templateData = [
                    'bride_name' => $invitation->bride_name,
                    'groom_name' => $invitation->groom_name,
                    'wedding_date' => date('d F Y', strtotime($invitation->wedding_date)),
                    'wedding_time' => $invitation->wedding_time,
                    'venue' => $invitation->venue ?? $invitation->location,
                    'location' => $invitation->location,
                    'additional_notes' => $invitation->additional_notes ?? 'Merupakan suatu kehormatan bagi kami apabila Bapak/Ibu berkenan hadir.',
                    'bride_father' => $invitation->bride_father ?? 'Bapak Bride Father',
                    'bride_mother' => $invitation->bride_mother ?? 'Ibu Bride Mother',
                    'groom_father' => $invitation->groom_father ?? 'Bapak Groom Father',
                    'groom_mother' => $invitation->groom_mother ?? 'Ibu Groom Mother',
                ];

                $startTime = microtime(true);
                
                // Test PDF generation
                $pdf = $pdfService->generateInvitationPdf($invitation, $templateData);
                
                $endTime = microtime(true);
                $duration = round(($endTime - $startTime) * 1000, 2);
                
                // Save test PDF
                $filename = "test_invitation_{$invitation->id}_" . time() . ".pdf";
                $testPath = storage_path('app/temp/' . $filename);
                
                // Create temp directory if it doesn't exist
                if (!file_exists(dirname($testPath))) {
                    mkdir(dirname($testPath), 0755, true);
                }
                
                $pdf->save($testPath);
                
                $fileSize = round(filesize($testPath) / 1024, 2); // KB
                
                $this->info("✅ PDF generated successfully!");
                $this->info("   Duration: {$duration}ms");
                $this->info("   File size: {$fileSize} KB");
                $this->info("   Saved to: {$testPath}");
                
                // Test HTML processing
                $originalHtml = $invitation->template->getCompiledHtml($templateData);
                $processedHtml = $pdfService->createPdfTemplate($originalHtml);
                
                $this->info("   Original HTML size: " . round(strlen($originalHtml) / 1024, 2) . " KB");
                $this->info("   Processed HTML size: " . round(strlen($processedHtml) / 1024, 2) . " KB");
                
            } catch (\Exception $e) {
                $this->error("❌ PDF generation failed!");
                $this->error("   Error: " . $e->getMessage());
                $this->error("   File: " . $e->getFile() . ":" . $e->getLine());
            }
            
            $this->newLine();
        }

        $this->info("PDF generation test completed.");
        
        // Show system info
        $this->newLine();
        $this->info("System Information:");
        $this->info("PHP Version: " . PHP_VERSION);
        $this->info("Memory Limit: " . ini_get('memory_limit'));
        $this->info("Max Execution Time: " . ini_get('max_execution_time'));
        
        return 0;
    }
}
