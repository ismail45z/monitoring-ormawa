<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect to their default dashboard if logged in but unauthorized
        $defaultRoute = match ($user->role) {
            'admin' => 'admin.dashboard',
            'pengurus_ormawa' => 'pengurus.dashboard',
            'mahasiswa_kip' => 'mahasiswa.dashboard',
            'wadir' => 'wadir.dashboard',
            default => 'login',
        };

        if ($defaultRoute !== 'login' && $request->route()->getName() !== $defaultRoute) {
            return redirect()->route($defaultRoute)->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        }

        abort(403, 'Unauthorized action.');
    }
}
