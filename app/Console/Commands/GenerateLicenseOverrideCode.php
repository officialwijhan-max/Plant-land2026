<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Generates a fresh override code for THIS deployment only - run once
 * per project (e.g. on your own machine before handing off, or on the
 * client's server if you have access at setup time), not something the
 * client runs themselves. See docs/LICENSING.md.
 *
 * Each project gets its own independent code - never reuse one code
 * across multiple client projects, since a single leaked code would
 * then compromise every project that shared it.
 */
class GenerateLicenseOverrideCode extends Command
{
    protected $signature = 'license:generate-override-code';
    protected $description = 'Generate a new emergency override code for this specific deployment';

    public function handle(): int
    {
        $code = Str::random(16);
        $hash = hash('sha256', $code);

        $this->info('Override code (give this to the client only during a genuine emergency, e.g. your license server being down):');
        $this->line($code);
        $this->newLine();
        $this->info('Add this to this deployment\'s .env as LICENSE_OVERRIDE_HASH:');
        $this->line($hash);
        $this->newLine();
        $this->warn('This code is specific to this one deployment. Do not reuse it for other client projects, and do not store the plain code anywhere except your own password manager.');

        return self::SUCCESS;
    }
}
