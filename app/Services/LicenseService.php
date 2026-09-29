<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Checks this installation's license status against AZ For Trade &
 * Marketing's license server. This is a disclosed licensing gate, not a
 * hidden mechanism - see docs/LICENSING.md for what it does, why it's
 * here, and how the emergency override works.
 *
 * Failure behavior is deliberately graduated, never an abrupt hard lock
 * from connectivity alone:
 *   - Unreachable, within the grace period: app runs normally.
 *   - Unreachable, past the grace period: app keeps running, with a
 *     visible warning banner instead of being blocked.
 *   - The license server explicitly returns "suspended"/"expired":
 *     that's the only case that hard-blocks the app.
 * A missed check or a network problem - including this vendor's own
 * server having downtime - must never be able to take down a client's
 * business on its own.
 */
class LicenseService
{
    private const CACHE_KEY = 'license_status';
    private const LAST_OK_KEY = 'license_last_verified_at';
    private const CACHE_MINUTES = 30;
    private const GRACE_PERIOD_HOURS = 48;
    private const OVERRIDE_FLAG_FILENAME = 'license_override.flag';

    private ?string $endpoint;
    private ?string $appId;
    private ?string $token;
    private ?string $secret;

    public function __construct()
    {
        $this->endpoint = config('services.license.endpoint');
        $this->appId = config('services.license.app_id');
        $this->token = config('services.license.token');
        $this->secret = config('services.license.secret');
    }

    /**
     * @return array{active: bool, status: string, message: ?string, warn_only: bool}
     */
    public function check(?string $tempCode = null): array
    {
        if ($this->isOverridden()) {
            return ['active' => true, 'status' => 'manual_override', 'message' => null, 'warn_only' => false];
        }

        if (! $this->endpoint || ! $this->appId || ! $this->token || ! $this->secret) {
            // Not configured - runs ungated rather than crashing, so this
            // works in local/dev environments or any deployment where
            // licensing isn't set up.
            return ['active' => true, 'status' => 'not_configured', 'message' => null, 'warn_only' => false];
        }

        if ($tempCode !== null) {
            $result = $this->callServer($tempCode);
            $this->clearCache();
            return $result;
        }

        $cached = Cache::get(self::CACHE_KEY);
        if ($cached !== null) {
            return $cached;
        }

        return $this->callServer();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Set by App\Console\Commands\LicenseOverride after validating a
     * per-deployment code against LICENSE_OVERRIDE_HASH - see that
     * command and docs/LICENSING.md. A plain file (checked with raw PHP
     * file functions, not the Storage/Flysystem facade - see
     * docs/LICENSING.md for why) rather than the cache store, so it
     * survives cache:clear/optimize:clear during normal maintenance
     * instead of silently re-enabling the check.
     */
    public function isOverridden(): bool
    {
        try {
            return file_exists($this->overrideFlagPath());
        } catch (\Throwable $e) {
            // This runs on every request via the middleware - a storage
            // permissions problem must never be able to take the whole
            // app down, same principle as the network-failure handling
            // in callServer().
            Log::warning('License check: could not read the override flag.', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * @throws \Throwable if the flag can't be written (surfaced to
     * whoever ran `license:override` rather than silently swallowed,
     * since here - unlike isOverridden() - failing silently would hide
     * a real problem from the person trying to fix one).
     */
    public function setOverride(string $note): void
    {
        $path = $this->overrideFlagPath();
        if (file_put_contents($path, $note . "\n" . now()->toIso8601String()) === false) {
            throw new \RuntimeException("Could not write override flag to {$path}.");
        }
    }

    public function clearOverride(): void
    {
        $path = $this->overrideFlagPath();
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function overrideFlagPath(): string
    {
        return storage_path('app/' . self::OVERRIDE_FLAG_FILENAME);
    }

    private function callServer(?string $tempCode = null): array
    {
        try {
            $timestamp = time();
            $signature = hash_hmac('sha256', $this->appId . $timestamp, $this->secret);

            $response = Http::timeout(8)
                ->withHeaders([
                    'X-App-Token' => $this->token,
                    'X-App-Id' => $this->appId,
                    'X-Timestamp' => $timestamp,
                    'X-Signature' => $signature,
                    'Accept' => 'application/json',
                ])
                ->post($this->endpoint, $tempCode ? ['temp_code' => $tempCode] : []);

            if ($response->successful()) {
                $data = $response->json() ?? [];
                $status = $data['status'] ?? 'active';
                $active = ! in_array($status, ['suspended', 'expired'], true);

                $result = [
                    'active' => $active,
                    'status' => $status,
                    'message' => $data['message'] ?? null,
                    'warn_only' => false,
                ];

                Cache::put(self::CACHE_KEY, $result, now()->addMinutes(self::CACHE_MINUTES));

                if ($active) {
                    Cache::put(self::LAST_OK_KEY, now()->toIso8601String(), now()->addDays(30));
                }

                return $result;
            }

            Log::warning('License check: license server returned a non-success response.', [
                'status_code' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('License check: could not reach the license server.', ['error' => $e->getMessage()]);
        }

        return $this->gracePeriodResult();
    }

    private function gracePeriodResult(): array
    {
        $lastOk = Cache::get(self::LAST_OK_KEY);

        if (! $lastOk) {
            // Never successfully verified yet (fresh install, before the
            // first check completes) - run normally rather than punish a
            // brand-new deployment for a momentary connectivity gap.
            return ['active' => true, 'status' => 'unverified', 'message' => null, 'warn_only' => false];
        }

        $hoursSinceLastOk = \Carbon\Carbon::parse($lastOk)->diffInHours(now());

        if ($hoursSinceLastOk < self::GRACE_PERIOD_HOURS) {
            return ['active' => true, 'status' => 'grace_period', 'message' => null, 'warn_only' => false];
        }

        // Past the grace period, but this alone never hard-blocks -
        // only an explicit suspended/expired response from the server
        // does that. Connectivity loss degrades to a visible warning.
        return [
            'active' => true,
            'status' => 'grace_expired',
            'message' => 'Unable to verify this installation\'s license for an extended period. Contact AZ For Trade & Marketing.',
            'warn_only' => true,
        ];
    }
}
