<?php

use App\Staff;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Claims the placeholder super admin row created by
     * database/migrations/2014_10_12_000000_create_users_table.php (id 1,
     * locked with a random unusable password) and gives it real, usable
     * credentials. That row has to exist at migration time because many
     * other migrations hardcode user_id/created_by = 1 as a foreign key.
     *
     * Safe to re-run: once this seeder has assigned the configured email to
     * that account, re-running never overwrites its password.
     *
     * Credentials come from ADMIN_EMAIL / ADMIN_USERNAME / ADMIN_NAME /
     * ADMIN_PASSWORD in .env. If ADMIN_PASSWORD is not set, a random one is
     * generated and printed once — it is never written to any file.
     *
     * @return void
     */
    public function run()
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        $admin = User::where('role_id', 1)->orderBy('id')->first();

        if (! $admin) {
            $this->command->error('No super admin (role_id 1) row found — run `php artisan migrate` first.');
            return;
        }

        if ($admin->email === $email) {
            $this->command->info("Super admin already configured: {$email} (password left unchanged).");
            Staff::firstOrCreate(['user_id' => $admin->id]);
            return;
        }

        $password = env('ADMIN_PASSWORD');
        $generated = empty($password);
        if ($generated) {
            $password = Str::random(20);
        }

        $admin->update([
            'name' => env('ADMIN_NAME', 'Super Admin'),
            'username' => env('ADMIN_USERNAME', 'admin'),
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
        ]);

        // Most of the app (sales, purchases, inventory, reports, POS) reads
        // Auth::user()->staff directly and 500s if it's null, so every user
        // needs a linked staff record.
        Staff::firstOrCreate(['user_id' => $admin->id]);

        $this->command->newLine();
        $this->command->warn('==================== SUPER ADMIN CREATED ====================');
        $this->command->warn(' Email:    ' . $email);
        if ($generated) {
            $this->command->warn(' Password: ' . $password);
            $this->command->warn(' This password is generated and is not stored anywhere.');
            $this->command->warn(' Save it now, then change it after first login.');
        } else {
            $this->command->warn(' Password: (taken from ADMIN_PASSWORD in your .env)');
        }
        $this->command->warn('===============================================================');
        $this->command->newLine();
    }
}
