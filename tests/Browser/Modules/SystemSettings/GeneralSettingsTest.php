<?php


namespace Tests\Browser\Modules\SystemSettings;


use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GeneralSettingsTest extends DuskTestCase
{
    use WithFaker;
    public function testVisitPageWithoutLogin(){

        $this->browse(function (Browser $browser){
            $browser->visit('/setting')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin(){

        $this->browse(function (Browser $browser){
            $browser->loginAs(1)
                ->visit('/setting')
                ->assertSee('Settings');
        });
    }

    public function testActivation(){
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated')
                ->pause(1000)
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated')
                ->pause(1000)

                ->click('#DataTables_Table_0 > tbody > tr:nth-child(2) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated')
                ->pause(1000)
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(2) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated')
                ->pause(1000)

                ->click('#DataTables_Table_0 > tbody > tr:nth-child(3) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated')
                ->pause(1000)
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(3) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated')
                ->pause(1000)

                ->click('#DataTables_Table_0 > tbody > tr:nth-child(4) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated')
                ->pause(1000)
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(4) > td:nth-child(3) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated')
                ->pause(1000);
        });
    }

    public function testGeneralSettings()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser->click('#General-tab')
                ->attach('#site_logo', public_path('img/profile.jpg'))
                ->attach('#favicon_logo', public_path('img/profile.jpg'))
                ->click('#General > form > div.submit_btn.text-center.mt-4 > button')
                ->waitFor('.toast-message')
                ->assertSee('GeneralSetting Credentials has been updated Successfully');
        });
    }

    public function testCompanySettings()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser->click('#Company_Information-tab')
                ->click('#Company_Information > div.col-12.mb-10.pt_15 > div > button')
                ->waitFor('.toast-message')
                ->assertSee('Successfully Updated');
        });
    }

    public function testInvoiceSettings()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser->click('#invoice-tab')
                ->click('#invoice > form > div.submit_btn.text-center.mt-4 > button')
                ->waitFor('.toast-message')
                ->assertSee('GeneralSetting Credentials has been updated Successfully');
        });
    }

    public function testSmtpSettings()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser->click('#SMTP-tab')
                ->click('#SMTP > form:nth-child(2) > div:nth-child(3) > div > div > div')
                ->click('#SMTP > form:nth-child(2) > div:nth-child(3) > div > div > div > ul > li:nth-child(1)')
                ->type('#smtp > div:nth-child(3) > div > input.primary_input_field', 'smtp.mailtrap.io')
                ->type('#smtp > div:nth-child(5) > div > input.primary_input_field', '3552ee915311b4')
                ->type('#smtp > div:nth-child(6) > div > input.primary_input_field', '3fa5b6bc69f13e')
                ->click('#smtp > div:nth-child(7) > div > div')
                ->click('#smtp > div:nth-child(7) > div > div > ul > li:nth-child(2)')
                ->click('#SMTP > form:nth-child(2) > div:nth-child(6) > div > div > button')
                ->waitFor('.toast-message')
                ->assertSee('SMTP Gateways Credentials has been updated Successfully');
        });
    }

    public function testSendTestEmail()
    {
        $this->testSmtpSettings();
        $this->browse(function (Browser $browser){
            $browser
                ->type('#SMTP > form:nth-child(3) > div.row > div:nth-child(1) > div > input', 'tariqulislamrc@gmail.com')
                ->type('#SMTP > form:nth-child(3) > div.row > div:nth-child(2) > div > input', $this->faker->paragraph)
                ->click('#SMTP > form:nth-child(3) > div.submit_btn.text-center.mb-100.pt_15 > button')
                ->waitFor('.toast-message')
                ->assertSee('Mail has been sent Successfully');
        });
    }

    public function testEmailTemplateSettings()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser->click('#Template-tab')
                ->click('#quotation_template > form > div.submit_btn.text-center.mb-100.pt_15 > button')

                ->waitFor('.toast-message')
                ->assertSee('Email Template has been updated Successfully');
        });
    }

    public function testSmsTemplateSettings()
    {
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser->click('#SMSTemplate-tab')
                ->click('#CustomerDueSMS > form > div.submit_btn.text-center.mb-100.pt_15 > button')

                ->waitFor('.toast-message')
                ->assertSee('SMS Template has been updated Successfully');
        });
    }

}
