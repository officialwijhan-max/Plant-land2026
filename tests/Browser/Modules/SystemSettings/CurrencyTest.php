<?php


namespace Tests\Browser\Modules\SystemSettings;


use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CurrencyTest extends DuskTestCase
{
    use WithFaker;

    public function testVisitPageWithoutLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/setting/currencies')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin()
    {

        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/setting/currencies')
                ->assertSee('Currency List');
        });
    }

    public function testAddNewCurrency()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#main-content > section > div > div > div.col-12 > div > div > ul > li > a')
                ->whenAvailable('#currency_add', function ($modal) {
                    $modal
                        ->type('#currency_addForm > div > div:nth-child(1) > div > input', 'Test')
                        ->type('#currency_addForm > div > div:nth-child(2) > div > input', 'te')
                        ->type('#currency_addForm > div > div:nth-child(3) > div > input', 'T')
                        ->click('#currency_addForm > div > div.col-lg-12.text-center > div > button');

                })
                ->waitFor('.toast-message')
                ->assertSee('Currency Added Successfully');
        });
    }


    public function testEditCurrency()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(5) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(5) > div > div > a:nth-child(1)')
                ->whenAvailable('#Item_Edit', function ($modal) {
                    $modal
                        ->click('#currencyEditForm > div > div.col-lg-12.text-center > div > button');

                })
                ->waitFor('.toast-message')
                ->assertSee('Currency Updated Successfully');
        });
    }


    public function testDeleteCurrency()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(5) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(5) > div > div > a:nth-child(2)')
                ->whenAvailable('#confirm-delete', function ($modal) {
                    $modal->click('#delete_link');
                })->waitFor('.toast-message')
                ->assertSee('Currency has been deleted Successfully');
        });
    }

}
