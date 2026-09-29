<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Locks a route to the Super Admin account (role_id 1) only.
 *
 * This is deliberately independent of the general `permission` middleware,
 * which bypasses all checks for any role with type 'system_user' (Admin,
 * and any future role like GM/CEO given broad access) - Settings needs a
 * harder boundary than that: literally nobody but Super Admin, regardless
 * of what type their role is or what's in the role_permission pivot.
 */
class EnsureSuperAdmin
{
    public function handle($request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if ((int) auth()->user()->role_id !== 1) {
            abort(403, 'Only the Super Admin account can access system settings.');
        }

        return $next($request);
    }
}
