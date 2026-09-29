<?php


namespace Tests\Browser\Modules\Accounts;


use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Traits\PurchaseTrait;
use Tests\DuskTestCase;

class IncomeTest extends DuskTestCase
{
    use PurchaseTrait;

    public function testVisitPageWithOutAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/account/income/create')
                ->assertPathIs('/login')
                ->visit('/account/income')
                ->assertPathIs('/login');
        });

    }

    public function testVisitPageWithAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/account/income/create')
                ->assertSee('Add New Income')
                ->visit('/account/income')
                ->assertSee('Income Lists');
        });
    }

    public function testAddIncome()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('session-data')
                ->visit('/account/income/create')
                ->assertSee('Add New Income')
                ->type("#startDate", Carbon::today()->format('m/d/Y'))


                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(2) > div > input', $this->faker->paragraph)


                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(3) > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(3) > div > div > ul > li:nth-child(2)')


                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(4) > div > input', 5000)
                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div.col-lg-12 > div > input', $this->faker->paragraph)
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(3) > div > div > button')
                ->waitFor('.toast-message', 20)
                ->assertSee('Income has been added Successfully')
            ;
        });
    }

    public function testEditIncome()
    {
        $this->testAddIncome();
        $this->browse(function (Browser $browser){
            $browser->visit('/account/income')
                ->assertSee('Income Lists')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(6) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(6) > div > div > a:nth-child(1)')
                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(3) > div:nth-child(4) > div > input', 5000)
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(4) > div > div > button')
                ->waitFor('.toast-message')
                ->assertSee('Income has been updated Successfully');
        });

    }

    public function testDeleteIncome()
    {
        $this->testAddIncome();
        $this->browse(function (Browser $browser){
            $browser->visit('/account/income')
                ->assertSee('Income Lists')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(6) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(6) > div > div > a:nth-child(2)')
                ->whenAvailable('#confirm-delete', function ($modal){
                    $modal->click('#delete_link');
                })

                ->waitFor('.toast-message')
                ->assertSee('Income has been deleted Successfully');
        });

    }

}
