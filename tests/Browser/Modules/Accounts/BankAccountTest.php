<?php


namespace Tests\Browser\Modules\Accounts;


use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Traits\PurchaseTrait;
use Tests\DuskTestCase;

class BankAccountTest extends DuskTestCase
{
    use PurchaseTrait;

    public function testVisitPageWithOutAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/account/bank_accounts')
                ->assertPathIs('/login');
        });

    }

    public function testVisitPageWithAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('session-data')
                ->visit('/account/bank_accounts')
                ->assertSee('Bank Accounts');
        });
    }

    public function testCreateNewBankAccount()
    {
        $this->testVisitPageWithAuth();
        $this->browse(function (Browser $browser) {
            $browser->click('#main-content > section > div > div > div.col-12 > div > div > ul > li:nth-child(1) > a')
                ->whenAvailable('#Item_Details', function ($modal) {
                    $modal
                        ->type('#chart_account_form > div > div:nth-child(1) > div > input', $this->faker->name)
                        ->type('#chart_account_form > div > div:nth-child(2) > div > input', $this->faker->name)
                        ->type('#chart_account_form > div > div:nth-child(3) > div > input', $this->faker->randomNumber(6))
                        ->type('#chart_account_form > div > div:nth-child(4) > div > input', $this->faker->name)
                        ->type('#chart_account_form > div > div:nth-child(5) > div > input', $this->faker->paragraph)
                        ->click('#save_button_parent');

                })
                ->waitFor('.toast-message')
                ->assertSee('New Account Added Successfully');
        });
    }

    public function testCreateNewBankAccountWithStatusDeActive()
    {
        $this->testVisitPageWithAuth();
        $this->browse(function (Browser $browser) {
            $browser->click('#main-content > section > div > div > div.col-12 > div > div > ul > li:nth-child(1) > a')
                ->whenAvailable('#Item_Details', function ($modal) {
                    $modal
                        ->type('#chart_account_form > div > div:nth-child(1) > div > input', $this->faker->name)
                        ->type('#chart_account_form > div > div:nth-child(2) > div > input', $this->faker->name)
                        ->type('#chart_account_form > div > div:nth-child(3) > div > input', $this->faker->randomNumber(6))
                        ->type('#chart_account_form > div > div:nth-child(4) > div > input', $this->faker->name)
                        ->type('#chart_account_form > div > div:nth-child(5) > div > input', $this->faker->paragraph)
                        ->click('#theme_nav > li:nth-child(2) > label > span')
                        ->click('#save_button_parent');

                })
                ->waitFor('.toast-message')
                ->assertSee('New Account Added Successfully');
        });
    }

    public function testEditNewBankAccountWithStatusChange()
    {
        $this->testCreateNewBankAccountWithStatusDeactive();
        $this->browse(function (Browser $browser) {
            $browser->visit('/account/bank_accounts')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(8) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(8) > div > div > a.dropdown-item.edit_chart_account')
                ->whenAvailable('#ChartAccount_Edit', function ($modal) {
                    $modal
                        ->click('#theme_nav > li:nth-child(1) > label > span')
                        ->click('#ChartAccountEditForm > div > div.col-lg-12.text-center > div > button');

                })
                ->waitFor('.toast-message')
                ->assertSee('Bank Account Updated Successfully');
        });
    }

    public function testEditNewBankAccount()
    {
        $this->testVisitPageWithAuth();
        $this->browse(function (Browser $browser) {
            $browser->visit('/account/bank_accounts')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(8) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(8) > div > div > a.dropdown-item.edit_chart_account')
                ->whenAvailable('#ChartAccount_Edit', function ($modal) {
                    $modal
                        ->click('#ChartAccountEditForm > div > div.col-lg-12.text-center > div > button');

                })
                ->waitFor('.toast-message')
                ->assertSee('Bank Account Updated Successfully');
        });
    }

    public function testDeleteBankAccount()
    {
        $this->testVisitPageWithAuth();
        $this->browse(function (Browser $browser) {
            $browser->visit('/account/bank_accounts')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(8) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr > td:nth-child(8) > div > div > a:nth-child(3)')
                ->whenAvailable('#confirm-delete', function ($modal) {
                    $modal->click('#delete_link');
                })
                ->waitFor('.toast-message')
                ->assertSee('Bank Account Deleted Successfully');
        });
    }

    public function testViewAccountHistory()
    {
        $this->testVisitPageWithAuth();
        $this->browse(function (Browser $browser) {
            $browser->visit('/account/bank_accounts')
                ->click('#DataTables_Table_0 > tbody > tr.odd > td:nth-child(8) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr > td:nth-child(8) > div > div > a:nth-child(1)')
                ->assertSee('Bank Account Details');
        });
    }

    public function testVisitOpeningBalancePageWithOutAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/account/voucher/openning-balance-create')
                ->assertPathIs('/login');
        });
    }

    public function testVisitOpeningBalancePageWithAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(1)
                ->visit('session-data')
                ->visit('/account/voucher/openning-balance-create')
                ->assertSee('Add New Opening Balance');
        });
    }

    public function testAddOpeningBalance()
    {
        $this->testVisitOpeningBalancePageWithAuth();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(1) > div > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(1) > div > div > div > ul > li:nth-child(2)')
                ->type('#startDate', Carbon::today()->format('m/d/Y'))
                ->type('#amount', 5000)
                ->click('#save')
                ->waitFor('.toast-message')
                ->assertSee('Opening Balance Added Successfully');
        });
    }

    public function testDuplicateOpeningBalance()
    {
        $this->testVisitOpeningBalancePageWithAuth();
        $this->browse(function (Browser $browser) {
            $browser
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(1) > div > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(1) > div > div > div > ul > li:nth-child(2)')
                ->type('#startDate', Carbon::today()->format('m/d/Y'))
                ->type('#amount', 5000)
                ->click('#save')
                ->waitFor('.toast-message')
                ->assertSee('Openning balance already add for this account');
        });
    }


}
