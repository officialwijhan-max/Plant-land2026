<?php


namespace Tests\Browser\Modules\SystemSettings;


use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PaymentMethodSettings extends DuskTestCase
{
    use WithFaker;
    public function testVisitPageWithoutLogin(){

        $this->browse(function (Browser $browser){
            $browser->visit('/setting/payment-method-settings')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin(){

        $this->browse(function (Browser $browser){
            $browser->loginAs(1)
                ->visit('/setting/payment-method-settings')
                ->assertSee('Select a payment gateway');
        });
    }

    public function testPaymentMethodSettings(){
        $this->browse(function (Browser $browser){
           $browser->click('#main-content > section > div > div > div.col-lg-3 > form > div > div:nth-child(2) > div > button')
               ->waitFor('.toast-message')
               ->assertSee('Operation successful')
               ->click('#Stripe > form > div > div.row.mt-40 > div > button')
               ->waitFor('.toast-message')
               ->assertSee('Operation successful');
        });
    }

}
