<?php


namespace Tests\Browser\Modules\SystemSettings;


use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IntroPrefixTest extends DuskTestCase
{
    use WithFaker;

    public function testVisitPageWithoutLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/setup/introPrefix')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setup/introPrefix')
                ->assertSee('Intro Prefix List');
        });
    }

    public function testEditIntroPrefix()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(4) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(4) > div > div > a')
                ->whenAvailable('#IntroPrefix_Edit', function ($modal){
                    $modal->click('#division_editForm > div > div.col-lg-12.text-center > div > button');
                })
                ->waitFor('.toast-message')
                ->assertSee('Intro Prefix has been updated Successfully');
        });

    }

}
