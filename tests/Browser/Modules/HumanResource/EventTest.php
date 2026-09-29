<?php


namespace Tests\Browser\Modules\HumanResource;


use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EventTest extends DuskTestCase
{
    use WithFaker;
    public function testVisitPageWithOutAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/events')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(1)
                ->visit('session-data')
                ->visit('/events')
                ->assertSee('Event List');
        });
    }

    public function testCreateEvent()
    {
        $this->testVisitPageWithAuth();
        $this->browse(function (Browser $browser) {
            $browser
                ->type('#main-content > section.admin-visitor-area.up_admin_visitor > div > div > div.col-lg-3 > div > div > form > div > div > div > div:nth-child(1) > div > input', $this->faker->title)
                ->type('#main-content > section.admin-visitor-area.up_admin_visitor > div > div > div.col-lg-3 > div > div > form > div > div > div > div:nth-child(3) > div > input', 'Rajshahi')
                ->type('#main-content > section.admin-visitor-area.up_admin_visitor > div > div > div.col-lg-3 > div > div > form > div > div > div > div:nth-child(6) > div > input', $this->faker->paragraph)
                ->click('#main-content > section.admin-visitor-area.up_admin_visitor > div > div > div.col-lg-3 > div > div > form > div > div > div > div.col-lg-12.text-center > button')

                ->waitFor('.toast-message')
                ->assertSee('Event Has Been Created Successfully');

        });
    }

    public function testEditEvent()
    {
        $this->testCreateEvent();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(6) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(6) > div > div > a:nth-child(1)')
                ->click('#main-content > section.admin-visitor-area.up_admin_visitor > div > div:nth-child(2) > div.col-lg-3 > div > div > form > div > div > div > div.col-lg-12.text-center > button')

                ->waitFor('.toast-message')
                ->assertSee('Event Has Been Updated Successfully');

        });
    }

    public function testDeleteEvent()
    {
        $this->testCreateEvent();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(6) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(6) > div > div > a:nth-child(2)')
                ->whenAvailable('#confirm-delete', function ($modal) {
                    $modal->click('#delete_link');
                })
                ->waitFor('.toast-message')

                ->assertSee('Event Has Been Deleted Successfully');

        });
    }

}
