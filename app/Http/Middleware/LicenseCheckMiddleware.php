<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;

/**
 * Blocks the application when the license check explicitly fails, or
 * injects a warning banner on prolonged connectivity loss. See
 * App\Services\LicenseService and docs/LICENSING.md for the full
 * behavior and the emergency override (App\Console\Commands\
 * LicenseOverride, run via `php artisan license:override`).
 *
 * Removing this class from the 'web' middleware group in
 * app/Http/Kernel.php disables the license check entirely, with no
 * other side effects on the application.
 */
class LicenseCheckMiddleware
{
    public function __construct(private readonly LicenseService $licenseService)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        // Temp access code submitted from the suspended page - this
        // round-trips to the license server for validation, it is not a
        // local secret check.
        if ($request->isMethod('POST') && $request->filled('_license_temp_code')) {
            $result = $this->licenseService->check($request->input('_license_temp_code'));
            if ($result['active']) {
                session(['license_temp_active_until' => now()->addHours(24)->toIso8601String()]);
                return redirect()->back();
            }
        }

        $tempActiveUntil = session('license_temp_active_until');
        if ($tempActiveUntil && now()->lt($tempActiveUntil)) {
            return $next($request);
        }
        session()->forget('license_temp_active_until');

        $result = $this->licenseService->check();

        if (! $result['active']) {
            return $this->hardBlock($request, $result);
        }

        $response = $next($request);

        if ($result['warn_only'] ?? false) {
            $this->injectBanner($response, $result['message']);
        }

        return $response;
    }

    private function hardBlock(Request $request, array $result)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $result['message'] ?? 'This installation\'s license is inactive.',
            ], 503);
        }

        return response()->view('errors.license-required', [
            'message' => $result['message'] ?? null,
        ], 503);
    }

    private function injectBanner($response, ?string $message): void
    {
        if (! method_exists($response, 'getContent') || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return;
        }

        $content = $response->getContent();
        if (! is_string($content) || ! str_contains($content, '</body>')) {
            return;
        }

        $safeMessage = e($message ?? 'License warning - contact AZ For Trade & Marketing.');
        $banner = '<div style="position:fixed;bottom:0;left:0;right:0;background:#854d0e;'
            . 'color:#fef9c3;padding:10px 20px;font-size:13px;text-align:center;z-index:9999;">'
            . $safeMessage . '</div>';

        $response->setContent(str_replace('</body>', $banner . '</body>', $content));
    }
}
