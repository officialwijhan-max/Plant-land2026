<?php


namespace Tests\Browser\Modules\Accounts;


use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Traits\PurchaseTrait;
use Tests\DuskTestCase;

class ExpenseTest extends DuskTestCase
{
    use PurchaseTrait;

    public function testVisitPageWithOutAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/account/expenses/create')
                ->assertPathIs('/login')
                ->visit('/account/expenses/index')
                ->assertPathIs('/login');
        });

    }

    public function testVisitPageWithAuth()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('/account/expenses/create')
                ->assertSee('Add New Expense')
                ->visit('/account/expenses/index')
                ->assertSee('Expense Lists');
        });
    }

    public function testAddExpenseFromCashSingleExpense()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('session-data')
                ->visit('/account/expenses/create')
                ->assertSee('Add New Expense')
                ->type("#startDate", Carbon::today()->format('m/d/Y'))

                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(2) > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(2) > div > div > ul > li:nth-child(2)')
                ->pause(5000)

                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div.col-lg-6.payment_from_div > div > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div.col-lg-6.payment_from_div > div > div > div > ul > li:nth-child(3)')


                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(4) > div > input', $this->faker->paragraph)


                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(1) > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(1) > div > div > ul > li:nth-child(2)')


                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div.col-lg-3 > div > input', 5000)
                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(3) > div > input', $this->faker->paragraph)
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(9) > div > div > button')
                ->waitFor('.toast-message', 20)
                ->assertSee('Expense has been added Successfully')
            ;
        });
    }

    public function testAddExpenseFromCashMultipleExpense()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('session-data')
                ->visit('/account/expenses/create')
                ->assertSee('Add New Expense')
                ->type("#startDate", Carbon::today()->format('m/d/Y'))

                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(2) > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(2) > div > div > ul > li:nth-child(2)')
                ->pause(5000)

                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div.col-lg-6.payment_from_div > div > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div.col-lg-6.payment_from_div > div > div > div > ul > li:nth-child(3)')


                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(2) > div:nth-child(4) > div > input', $this->faker->paragraph)


                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(1) > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(1) > div > div > ul > li:nth-child(2)')


                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div.col-lg-3 > div > input', 5000)
                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(3) > div > input', $this->faker->paragraph)

                ->click('#add_payment_to_form')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(5) > div > div')
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(5) > div > div > ul > li:nth-child(3)')

                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div.col-lg-3.row_id_1 > div > input', 5000)
                ->type('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div:nth-child(7) > div > input', $this->faker->paragraph)


                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div:nth-child(9) > div > div > button')
                ->waitFor('.toast-message', 20)
                ->assertSee('Expense has been added Successfully')
            ;
        });
    }

    public function testAddExpenseRemoveRow()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit('session-data')
                ->visit('/account/expenses/create')
                ->assertSee('Add New Expense')



                ->click('#add_payment_to_form')
                ->pause(1000)
                ->click('#add_payment_to_form')
                ->pause(1000)
                ->click('#add_payment_to_form')
                ->pause(1000)
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div.col-lg-1.row_id_1 > div > button')
                ->pause(1000)
                ->click('#add_payment > section > div > div > div:nth-child(2) > div > form > div.row.form > div.col-lg-1.row_id_2 > div > button')


            ;
        });
    }

    public function testShowExpenseList()
    {
        $this->testAddExpenseFromCashMultipleExpense();
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/account/expenses/index')
                ->assertSee('Expense Lists');
        });
    }

}
