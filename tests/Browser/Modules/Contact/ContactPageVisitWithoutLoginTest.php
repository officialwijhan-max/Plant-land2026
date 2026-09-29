<?php

namespace Tests\Browser\Modules\Contact;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContactPageVisitWithoutLoginTest extends DuskTestCase
{
    use withFaker;



    public function testVisitSupplierPageWithOutLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('contact/supplier')
                ->assertPathIs('/login');
        });
    }


    public function testVisitCustomerPageWithOutLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('contact/customer')
                ->assertPathIs('/login');
        });
    }

    public function testVisitAddContactPageWithOutLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('contact/add_contact')
                ->assertPathIs('/login');
        });
    }

    public function testVisitSettingPageWithOutLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('contact/settings')
                ->assertPathIs('/login');
        });
    }

    public function testVisitWalkingCustomerDetailsPageWithOutLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('contact/customer/details/1')
                ->assertPathIs('/login');
        });
    }


}
