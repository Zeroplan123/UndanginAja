<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Services\PdfExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CommunicationController extends Controller
{
    /**
     * Send invitation via email with PDF attachment
     */
    public function sendEmail(Request $request, $slug, PdfExportService $pdfService)
    {
        // Check if email feature is under maintenance
        if (config('app.email_maintenance', true)) {
            return back()->with('error', 'Fitur pengiriman email sedang dalam maintenance. Silakan gunakan WhatsApp atau download PDF untuk sementara.');
        }

        $request->validate([
            'emails' => 'required|string',
            'recipient_names' => 'nullable|string',
            'custom_message' => 'nullable|string|max:500',
            'include_pdf' => 'nullable|boolean'
        ]);

        $invitation = Invitation::where('slug', $slug)
                               ->where('user_id', auth()->id())
                               ->with('template')
                               ->firstOrFail();

        // Parse emails and names
        $emails = array_filter(array_map('trim', explode(',', $request->emails)));
        $names = array_filter(array_map('trim', explode(',', $request->recipient_names ?? '')));

        $successCount = 0;
        $errors = [];
        $pdfPath = null;

        // Generate PDF if requested
        if ($request->include_pdf) {
            try {
                $templateData = [
                    'bride_name' => $invitation->bride_name,
                    'groom_name' => $invitation->groom_name,
                    'wedding_date' => date('d F Y', strtotime($invitation->wedding_date)),
                    'wedding_time' => $invitation->wedding_time,
                    'venue' => $invitation->venue ?? $invitation->location,
                    'location' => $invitation->location,
                    'additional_notes' => $invitation->additional_notes,
                    'bride_father' => $invitation->bride_father,
                    'bride_mother' => $invitation->bride_mother,
                    'groom_father' => $invitation->groom_father,
                    'groom_mother' => $invitation->groom_mother,
                ];
                
                $pdfPath = $pdfService->generateForEmail($invitation, $templateData);
                \Log::info("PDF berhasil dibuat: {$pdfPath}");
            } catch (\Exception $e) {
                \Log::error("Gagal membuat PDF: " . $e->getMessage());
                return back()->with('error', 'Gagal membuat PDF attachment. Silakan coba lagi.');
            }
        }

        foreach ($emails as $index => $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email tidak valid: {$email}";
                continue;
            }

            try {
                $recipientName = $names[$index] ?? null;
                
                // Debug: Log before sending
                \Log::info("Mencoba mengirim email ke: {$email}");
                
                // Send email with or without PDF attachment
                $mailResult = Mail::to($email)->send(new InvitationMail($invitation, $pdfPath, $recipientName));
                
                // Debug: Check if mail was actually sent
                \Log::info("Mail result: " . json_encode($mailResult));
                \Log::info("Mail failures: " . json_encode(Mail::failures()));
                
                $successCount++;
                
                // Log successful email for debugging
                \Log::info("Email berhasil dikirim ke: {$email}");
                
            } catch (\Exception $e) {
                $errorMessage = "Gagal mengirim ke {$email}: " . $e->getMessage();
                $errors[] = $errorMessage;
                
                // Log error for debugging
                \Log::error("Email gagal dikirim: " . $errorMessage, [
                    'email' => $email,
                    'exception' => $e->getTraceAsString()
                ]);
            }
        }

        // Clean up temporary PDF file
        if ($pdfPath && file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        if ($successCount > 0) {
            $message = "Berhasil mengirim {$successCount} undangan via email.";
            if (!empty($errors)) {
                $message .= " Namun ada " . count($errors) . " yang gagal.";
            }
            return back()->with('success', $message);
        } else {
            return back()->with('error', 'Semua pengiriman email gagal: ' . implode(', ', $errors));
        }
    }

    /**
     * Generate WhatsApp share link with invitation details
     */
    public function generateWhatsAppLink(Request $request, $slug)
    {
        $request->validate([
            'phone_numbers' => 'required|string',
            'custom_message' => 'nullable|string|max:300'
        ]);

        $invitation = Invitation::where('slug', $slug)
                               ->where('user_id', auth()->id())
                               ->firstOrFail();

        $defaultMessage = "🌸 *UNDANGAN PERNIKAHAN* 🌸\n\n" .
                         "Assalamu'alaikum Wr. Wb.\n" .
                         "Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami:\n\n" .
                         "👰🏻‍♀️ *{$invitation->bride_name}*\n" .
                         "Putri dari Bapak " . ($invitation->bride_father ?? '...') . " & Ibu " . ($invitation->bride_mother ?? '...') . "\n\n" .
                         "🤵🏻‍♂️ *{$invitation->groom_name}*\n" .
                         "Putra dari Bapak " . ($invitation->groom_father ?? '...') . " & Ibu " . ($invitation->groom_mother ?? '...') . "\n\n" .
                         "📅 *Hari/Tanggal:* " . date('l, d F Y', strtotime($invitation->wedding_date)) . "\n";
        
        if ($invitation->wedding_time) {
            $defaultMessage .= "🕐 *Waktu:* {$invitation->wedding_time}\n";
        }
        
        $defaultMessage .= "📍 *Tempat:* " . ($invitation->venue ?? $invitation->location) . "\n\n";
        
        if ($invitation->additional_notes) {
            $defaultMessage .= "📝 *Catatan:* {$invitation->additional_notes}\n\n";
        }
        
        $defaultMessage .= "Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada kedua mempelai.\n\n" .
                          "Atas kehadiran dan doa restunya, kami ucapkan terima kasih.\n\n" .
                          "Wassalamu'alaikum Wr. Wb.\n\n" .
                          "🤵🏻‍♀️👰🏻‍♀️ *{$invitation->groom_name} & {$invitation->bride_name}*";

        $message = $request->custom_message ?? $defaultMessage;
        $encodedMessage = urlencode($message);

        // Parse phone numbers
        $phoneNumbers = array_filter(array_map('trim', explode(',', $request->phone_numbers)));
        $whatsappLinks = [];

        foreach ($phoneNumbers as $phone) {
            // Clean phone number (remove spaces, dashes, etc.)
            $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
            
            // Add country code if not present
            if (!str_starts_with($cleanPhone, '+')) {
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '+62' . substr($cleanPhone, 1);
                } elseif (!str_starts_with($cleanPhone, '62')) {
                    $cleanPhone = '+62' . $cleanPhone;
                } else {
                    $cleanPhone = '+' . $cleanPhone;
                }
            }

            $whatsappLinks[] = [
                'phone' => $phone,
                'clean_phone' => $cleanPhone,
                'url' => "https://wa.me/{$cleanPhone}?text={$encodedMessage}"
            ];
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'links' => $whatsappLinks
        ]);
    }

    /**
     * Send invitation via WhatsApp Web (opens multiple tabs)
     */
    public function sendWhatsApp(Request $request, $slug)
    {
        $response = $this->generateWhatsAppLink($request, $slug);
        $data = $response->getData(true);

        if ($data['success']) {
            return back()->with('whatsapp_links', $data['links'])
                        ->with('whatsapp_message', $data['message'])
                        ->with('success', 'Link WhatsApp berhasil dibuat! Silakan klik link untuk mengirim.');
        }

        return back()->with('error', 'Gagal membuat link WhatsApp.');
    }

    /**
     * Generate temporary public PDF for WhatsApp sharing
     */
    public function generateTempPDF($slug, PdfExportService $pdfService)
    {
        $invitation = Invitation::where('slug', $slug)
                               ->where('user_id', auth()->id())
                               ->with('template')
                               ->firstOrFail();

        try {
            $templateData = [
                'bride_name' => $invitation->bride_name,
                'groom_name' => $invitation->groom_name,
                'wedding_date' => date('d F Y', strtotime($invitation->wedding_date)),
                'wedding_time' => $invitation->wedding_time,
                'venue' => $invitation->venue ?? $invitation->location,
                'location' => $invitation->location,
                'additional_notes' => $invitation->additional_notes,
                'bride_father' => $invitation->bride_father,
                'bride_mother' => $invitation->bride_mother,
                'groom_father' => $invitation->groom_father,
                'groom_mother' => $invitation->groom_mother,
            ];

            // Generate PDF using the service
            $pdf = $pdfService->generateInvitationPdf($invitation, $templateData);
            
            // Create public temporary file
            $publicFilename = 'temp_invitation_' . $invitation->slug . '_' . time() . '.pdf';
            $publicPath = public_path('temp/' . $publicFilename);
            
            // Create temp directory if it doesn't exist
            if (!file_exists(dirname($publicPath))) {
                mkdir(dirname($publicPath), 0755, true);
            }

            // Save PDF to public directory
            $pdf->save($publicPath);
            
            // Schedule cleanup after 24 hours (you might want to implement this with a job)
            $publicUrl = url('temp/' . $publicFilename);

            return response()->json([
                'success' => true,
                'pdf_url' => $publicUrl,
                'expires_in' => '24 jam'
            ]);
        } catch (\Exception $e) {
            \Log::error('Temp PDF Generation Error: ' . $e->getMessage(), [
                'invitation_id' => $invitation->id,
                'slug' => $slug,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF. Silakan coba lagi.'
            ], 500);
        }
    }


    /**
     * Show communication page for specific invitation
     */
    public function show($slug)
    {
        $invitation = Invitation::where('slug', $slug)
                               ->where('user_id', auth()->id())
                               ->with('template')
                               ->firstOrFail();

        return view('user.communication', compact('invitation'));
    }
}
