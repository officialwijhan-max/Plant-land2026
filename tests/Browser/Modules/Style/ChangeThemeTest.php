<?php


namespace Tests\Browser\Modules\Style;


use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChangeThemeTest extends DuskTestCase
{
    public function testVisitPageWithoutLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/style/themes/change_view')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/style/themes/change_view')
                ->assertSee('Change View');
        });
    }

    public function testChangeToCompact()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#contact_settings > section > div > div > div:nth-child(2) > div > form > div > div > div > div.col-lg-9 > div > div > div:nth-child(2) > div > label')
            ->click('#_submit_btn_admission')
            ->waitFor('.toast-message')
            ->assertSee('Compact view changed as default view');
        });
    }

    public function testChangeToNormal()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#contact_settings > section > div > div > div:nth-child(2) > div > form > div > div > div > div.col-lg-9 > div > div > div:nth-child(1) > div > label')
            ->click('#_submit_btn_admission')
            ->waitFor('.toast-message')
            ->assertSee('Normal view changed as default view');
        });
    }

}
