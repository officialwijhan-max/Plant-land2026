<?php

namespace Tests\Browser\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ForgotPasswordTest extends DuskTestCase
{
    public function testVisitPageFromLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('login')
                ->clickLink('Forget Password')
                ->assertRouteIs('password.request');
        });
    }

    public function testEmptyEmail()
    {

        $this->browse(function ($browser) {
            $browser->visitRoute('password.request')
                ->click('#content_form > button.login-res-btn.submit')
                ->assertSee('This value is required.');
        });

    }

    public function testInvalidEmail()
    {

        $this->browse(function ($browser) {
            $browser->visitRoute('password.request')
                ->type('email', 'spn4@notemail.com')
                ->click('#content_form > button.login-res-btn.submit')
                ->waitForText('We can not find a user with that email address.')
                ->assertSee('We can not find a user with that email address.');
        });

    }

/*    public function testSendResetPasswordLink()
    {

        $this->browse(function ($browser) {
            $browser->visitRoute('password.request')
                ->type('email', 'support@spondonit.com')
                ->click('#content_form > button.primary-btn.semi_large2.fix-gr-bg.submit.w-100.mb_20')
                ->pause(5000)
                ->waitFor('.toast-message')
                ->assertSee('We have emailed your password reset link!');
        });

    }*/

    public function testBackToLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('password.request')
                ->clickLink('Login')
                ->assertRouteIs('login');
        });
    }
}
