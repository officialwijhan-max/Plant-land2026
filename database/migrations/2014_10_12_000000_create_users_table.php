<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\User;
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('notification_preference')->default('mail');
            $table->boolean('is_active')->default(TRUE);
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Plenty of later migrations hardcode user_id/created_by = 1 (workspaces,
        // teams, contacts, chart accounts, ...), so a row at id 1 must exist
        // here at migration time. It's a locked placeholder with an
        // unguessable random password (never displayed) — real credentials
        // are set by database/seeds/SuperAdminSeeder.php via `php artisan
        // db:seed`, which claims this row instead of creating a duplicate.
        // This replaces what used to be a fixed, publicly-known password.
        User::create([
            'name' => 'Super Admin',
            'role_id' => 1,
            'password' => Hash::make(Str::random(40)),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
