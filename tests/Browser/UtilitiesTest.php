<?php


namespace Tests\Browser;


use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UtilitiesTest extends DuskTestCase
{
    public function testVisitPageWithoutLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/utilities')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/utilities')
                ->assertSee('Utilities');
        });
    }

    public function testClearCache()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#main-content > div.row.justify-content-center > div.col-lg-12 > div > div:nth-child(1) > a')
                ->waitFor('.toast-message')

                ->assertSee('Cache Files Cleared Successful');
        });
    }

    public function testClearLog()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#main-content > div.row.justify-content-center > div.col-lg-12 > div > div:nth-child(2) > a')
                ->waitFor('.toast-message')

                ->assertSee('Log Cleared Successful');
        });
    }


}
