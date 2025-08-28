<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestEmailController extends Controller
{
    /**
     * Test email configuration and sending
     */
    public function testEmail(Request $request)
    {
        try {
            // Check mail configuration
            $mailConfig = [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ];

            // Test basic email sending
            $testEmail = $request->input('email', 'test@example.com');
            
            Mail::raw('Test email dari UndanginAja. Jika Anda menerima email ini, konfigurasi email sudah benar.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email - UndanginAja');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email berhasil dikirim',
                'config' => $mailConfig,
                'sent_to' => $testEmail
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test email gagal: ' . $e->getMessage(),
                'config' => $mailConfig ?? [],
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
        }
    }

    /**
     * Show email test form
     */
    public function showTestForm()
    {
        return view('test.email');
    }
}
