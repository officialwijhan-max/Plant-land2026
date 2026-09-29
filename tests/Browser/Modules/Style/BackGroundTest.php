<?php


namespace Tests\Browser\Modules\Style;


use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BackGroundTest extends DuskTestCase
{
    use WithFaker;

    public function testVisitPageWithoutLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/style/update-bg')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/style/update-bg')
                ->assertSee('Background');
        });
    }

    public function testChangeBackground()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser) {
            $browser
                ->attach('#updateLoginBG > div.General_system_wrap_area.bg_grib.d-flex > div:nth-child(1) > div > div.update_logo_btn > button > input[type=file]', public_path('img/profile.jpg'))
                ->attach('#updateLoginBG > div.General_system_wrap_area.bg_grib.d-flex > div:nth-child(2) > div > div.update_logo_btn > button > input[type=file]', public_path('img/profile.jpg'))
                ->click('#updateLoginBG > div.submit_btn.text-center.mt-4 > button.primary_btn_large.submit')
                ->assertSee('Background image updated Successfully');
        });
    }
}
