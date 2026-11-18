<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Resources\UserResource;
use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CheckForceLogout
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Cek jika user di-force logout
            if ($user->force_logout) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                Auth::guard('web')->logout();
                
                // Kirim response khusus untuk force logout
                return response()->json([
                    'success' => false,
                    'message' => 'Anda telah di-logout secara paksa oleh administrator.'
                ], Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }
}