<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Illuminate\Console\Command;

/**
 * Verifies this installation's license status against the license
 * server. See docs/LICENSING.md. Deliberately visible in `php artisan
 * list` - this is a disclosed system, not something to hide.
 */
class CheckLicense extends Command
{
    protected $signature = 'license:check {--force : Bypass the cache and check the license server now}';
    protected $description = "Verify this installation's license status";

    public function handle(LicenseService $licenseService): int
    {
        if ($this->option('force')) {
            $licenseService->clearCache();
        }

        $result = $licenseService->check();

        $this->info('License status: ' . $result['status']);
        if (! empty($result['message'])) {
            $this->line($result['message']);
        }

        return $result['active'] ? self::SUCCESS : self::FAILURE;
    }
}
