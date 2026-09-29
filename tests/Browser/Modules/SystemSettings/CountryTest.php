<?php


namespace Tests\Browser\Modules\SystemSettings;


use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CountryTest extends DuskTestCase
{
    use WithFaker;

    public function testVisitPageWithoutLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/setup/country')
                ->assertPathIs('/login')
                ->visit('/setup/country/create')
                ->assertPathIs('/login')
                ->visit('/setup/state')
                ->assertPathIs('/login')
                ->visit('/setup/state/create')
                ->assertPathIs('/login')
                ->visit('/setup/city')
                ->assertPathIs('/login')
                ->visit('/setup/city/create')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setup/country')
                ->assertSee('Country')
                ->visit('/setup/state')
                ->assertSee('State')
                ->visit('/setup/city')
                ->assertSee('State');
        });
    }

    public function testCreateCountry()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setup/country/create')
                ->assertSee('New Country')
                ->type('#name', $this->faker->name)
                ->type('#code', 'TC')
                ->click('#content_form > div.text-center.mt-3 > button.primary-btn.semi_large2.fix-gr-bg.submit')
                ->pause(5000)
                ->assertPathIs('/setup/country/create');
        });
    }

    public function testEditCountry()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setup/country/1/edit')
                ->assertSee('Update Country')
                ->click('#content_form > div.text-center.mt-3 > button.primary-btn.semi_large2.fix-gr-bg.submit')
                ->pause(5000)
                ->assertPathIs('/setup/country');
        });
    }

    public function testCreateState()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setup/state/create')
                ->assertSee('New State')
                ->type('#name', $this->faker->name)
                ->click('#content_form > div.text-center.mt-3 > button.primary-btn.semi_large2.fix-gr-bg.submit')
                ->pause(5000)
                ->assertPathIs('/setup/state/create');
        });
    }

    public function testEditState()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setup/state/1/edit')
                ->assertSee('Update State')
                ->click('#content_form > div.text-center.mt-3 > button.primary-btn.semi_large2.fix-gr-bg.submit')
                ->pause(5000)
                ->assertPathIs('/setup/state');
        });
    }

    public function testCreateCity()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setup/city/create')
                ->assertSee('New City')
                ->type('#name', $this->faker->name)
                ->click('#content_form > div.text-center.mt-3 > button.primary-btn.semi_large2.fix-gr-bg.submit')
                ->pause(5000)
                ->assertPathIs('/setup/city/create');
        });
    }

    public function testEditCity()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setup/city/1/edit')
                ->assertSee('Update City')
                ->click('#content_form > div.text-center.mt-3 > button.primary-btn.semi_large2.fix-gr-bg.submit')
                ->pause(5000)
                ->assertPathIs('/setup/city');
        });
    }

}
