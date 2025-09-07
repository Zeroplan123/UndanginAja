<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class RateLimitUploads
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $key = 'upload_limit_' . $user->id;
        
        // Get current upload count for this hour
        $uploads = Cache::get($key, 0);
        
        // Check if limit exceeded
        if ($uploads >= 50) {
            return back()->withErrors([
                'photos' => 'Upload limit reached. You can upload maximum 50 photos per hour.'
            ]);
        }
        
        // Increment counter
        Cache::put($key, $uploads + 1, now()->addHour());
        
        return $next($request);
    }
}
