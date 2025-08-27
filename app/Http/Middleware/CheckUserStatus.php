<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is banned
            if ($user->status === 'banned') {
                // Check if ban has expired
                if ($user->ban_expires_at && Carbon::now()->greaterThan($user->ban_expires_at)) {
                    // Ban has expired, unban the user
                    $user->update([
                        'status' => 'active',
                        'ban_reason' => null,
                        'banned_at' => null,
                        'ban_expires_at' => null
                    ]);
                } else {
                    // User is still banned, logout and redirect
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    $banMessage = 'Akun Anda telah dibanned.';
                    if ($user->ban_reason) {
                        $banMessage .= ' Alasan: ' . $user->ban_reason;
                    }
                    if ($user->ban_expires_at) {
                        $banMessage .= ' Ban berakhir pada: ' . $user->ban_expires_at->format('d M Y H:i');
                    } else {
                        $banMessage .= ' Ban bersifat permanen.';
                    }
                    
                    return redirect('/login')->withErrors([
                        'email' => $banMessage
                    ]);
                }
            }
            
            // Check if user is suspended
            if ($user->status === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect('/login')->withErrors([
                    'email' => 'Akun Anda sedang disuspend. Silakan hubungi administrator.'
                ]);
            }
        }
        
        return $next($request);
    }
}
