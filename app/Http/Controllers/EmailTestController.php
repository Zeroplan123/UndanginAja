<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailTestController extends Controller
{
    public function testDirectEmail(Request $request)
    {
        $email = $request->input('email', 'test@example.com');
        
        try {
            // Test direct email sending
            Log::info("Testing direct email to: {$email}");
            
            $result = Mail::raw('Test email dari UndanginAja - Direct Send', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Direct Email - UndanginAja')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            Log::info("Mail send result: " . json_encode($result));
            Log::info("Mail failures: " . json_encode(Mail::failures()));
            
            // Check mail configuration
            $config = [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'from_address' => config('mail.from.address'),
                'queue_driver' => config('queue.default')
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Email test berhasil dikirim',
                'config' => $config,
                'failures' => Mail::failures(),
                'result' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error("Email test error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Email test gagal: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ]);
        }
    }
}
