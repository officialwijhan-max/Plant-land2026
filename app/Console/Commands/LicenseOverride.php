<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Illuminate\Console\Command;

/**
 * Emergency manual override for the license check - see
 * docs/LICENSING.md. Run only from the server's own command line by
 * whoever has shell access (never through a public web endpoint), with
 * a code specific to THIS deployment - AZ generates a unique code per
 * project with `php artisan license:generate-override-code` and keeps
 * it privately; it is never shared across projects or stored in plain
 * text anywhere.
 */
class LicenseOverride extends Command
{
    protected $signature = 'license:override {code? : The override code for this deployment} {--clear : Remove an active override, re-enabling the license check}';
    protected $description = 'Manually override the license check for this installation (emergency use only)';

    public function handle(LicenseService $licenseService): int
    {
        if ($this->option('clear')) {
            $licenseService->clearOverride();
            $this->info('License override cleared. The license check is active again.');
            return self::SUCCESS;
        }

        $code = $this->argument('code');
        if (! $code) {
            $this->error('An override code is required. Usage: php artisan license:override <code>');
            return self::FAILURE;
        }

        $expectedHash = config('services.license.override_hash');
        if (! $expectedHash) {
            $this->error('No LICENSE_OVERRIDE_HASH is configured for this deployment. Contact AZ For Trade & Marketing to obtain one.');
            return self::FAILURE;
        }

        if (! hash_equals($expectedHash, hash('sha256', $code))) {
            $this->error('Invalid override code.');
            return self::FAILURE;
        }

        $licenseService->setOverride('Manually overridden via license:override on ' . gethostname());
        $this->warn('License check overridden. The application will run without license verification until `php artisan license:override --clear` is run.');

        return self::SUCCESS;
    }
}
