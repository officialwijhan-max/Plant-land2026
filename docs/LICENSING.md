# Licensing

This installation includes a licensing check, maintained by AZ For Trade &
Marketing, comparable to how commercial Laravel packages (Nova, Statamic,
Spatie's paid packages) verify a subscription is active. This document
describes exactly what it does. Nothing about it is hidden or obfuscated.

## What it does

- `app/Services/LicenseService.php` periodically checks this installation's
  license status against AZ's license server (`LICENSE_ENDPOINT` in `.env`).
- `app/Http/Middleware/LicenseCheckMiddleware.php` runs on every web
  request. If the check comes back inactive, it blocks access with a
  clear "License Required" page instead of the app's normal content.
- `php artisan license:check` runs the check manually; it's also scheduled
  every 30 minutes in `app/Console/Kernel.php`.

## Failure behavior (read this before assuming it's "strict")

A missed check or lost connectivity - including AZ's own server having
downtime - **never blocks the app on its own**:

| Situation | Result |
|---|---|
| License server says "active" | Runs normally |
| License server explicitly says "suspended" or "expired" | Blocked, with a message |
| License server unreachable, less than 48 hours | Runs normally |
| License server unreachable, more than 48 hours | Runs normally, with a visible warning banner |
| `LICENSE_ENDPOINT`/`APP_ID`/`TOKEN`/`SECRET` not set in `.env` | Runs normally, check is disabled entirely |

The only way this blocks the app is an explicit "suspended"/"expired"
response from the license server. Connectivity problems degrade to a
warning, never a hard lock.

## Emergency override

If AZ's license server is genuinely unreachable for an extended period
and this is affecting a paying client, the check can be manually disabled
on that specific server:

```bash
php artisan license:override <code>
```

The code is unique to this one deployment (`LICENSE_OVERRIDE_HASH` in
`.env`), generated once via `php artisan license:generate-override-code`
and never reused across projects. It's run from the server's own command
line by whoever already has shell access - never through a public web
form - so it doesn't add any attack surface beyond what shell access
already implies.

To re-enable the check after resolving the issue:

```bash
php artisan license:override --clear
```

## Removing this entirely

This has no dependency on the rest of the application - removing it has
no other side effects:

1. Delete `app/Services/LicenseService.php`, `app/Http/Middleware/LicenseCheckMiddleware.php`,
   `app/Console/Commands/CheckLicense.php`, `app/Console/Commands/LicenseOverride.php`,
   `app/Console/Commands/GenerateLicenseOverrideCode.php`, and
   `resources/views/errors/license-required.blade.php`.
2. Remove `\App\Http\Middleware\LicenseCheckMiddleware::class` from the
   `web` group in `app/Http/Kernel.php`.
3. Remove the `license:check` schedule line from `app/Console/Kernel.php`.
4. Remove the `license` block from `config/services.php`.
5. Remove the `LICENSE_*` lines from `.env`.

Enforcement beyond that point is a matter of the license agreement
between AZ For Trade & Marketing and the client, not the code.

## Implementation note: why the override flag uses raw file functions

`isOverridden()` checks a plain flag file (`storage/app/license_override.flag`)
using PHP's own `file_exists()`/`file_put_contents()` rather than Laravel's
`Storage` facade. During local testing, `Storage::disk('local')` was
observed returning stale results for this exact check under a PHP
built-in dev server after a config change - raw file functions were
tested extensively across every failure scenario in this doc and
behaved consistently, so this uses the simpler option for what is only
ever a single-file existence/write/delete check.
