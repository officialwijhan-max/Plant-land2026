<?php

namespace Tests\Browser\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
//    use DatabaseMigrations;

    public function testInvalidLogin()
    {
        $this->browse(function (Browser $browser) {
            $this->browse(function ($browser) {
                $browser->visitRoute('login')
                    ->type('email', 'spn4@notemail.com')
                    ->type('password', '123456')
                    ->click('#content_form6 > button.login-res-btn.submit')
                    ->waitFor('.toast-message')
                    ->assertSee(trans('auth.failed'));
            });
        });
    }

    public function testEmptyEmailLogin()
    {
        $this->browse(function (Browser $browser) {
            $this->browse(function ($browser) {
                $browser->visitRoute('login')
                    ->type('password', '123456')
                    ->click('#content_form6 > button.login-res-btn.submit')
                    ->assertSee('This value is required.');
            });
        });
    }

    public function testEmptyEmailPassword()
    {
        $this->browse(function (Browser $browser) {
            $this->browse(function ($browser) {
                $browser->visitRoute('login')
                    ->type('email', 'support@spondonit.com')
                    ->click('#content_form6 > button.login-res-btn.submit')
                    ->assertSee('This value is required.');
            });
        });
    }

    public function testSuccessfulLogin()
    {
        $this->browse(function (Browser $browser) {
            $this->browse(function ($browser) {
                $browser->visitRoute('login')
                    ->type('email', 'support@spondonit.com')
                    ->type('password', '12345678')
                    ->click('#content_form6 > button.login-res-btn.submit')
                    ->pause(3000)
                    ->assertPathIs('/home')
                    ->assertSee(trans('Quick Summery'));
            });
        });
    }
}
