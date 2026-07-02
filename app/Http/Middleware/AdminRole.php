<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin role required.'
                ], 403);
            }

            return redirect()->route('admin.login')
                ->with('error', 'Akses ditolak. Anda harus login sebagai Admin.');
        }

        return $next($request);
    }
}
