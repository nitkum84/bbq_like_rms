<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware {
    public function handle(Request $request, Closure $next) {
        if (!auth()->check() || !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Access Denied. Admin only area.');
        }
        return $next($request);
    }
}
