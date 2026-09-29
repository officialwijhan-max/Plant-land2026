<?php


namespace Tests\Browser\Modules\SystemSettings;


use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LanguageTest extends DuskTestCase
{

    use WithFaker;
    public function testVisitPageWithoutLogin(){

        $this->browse(function (Browser $browser){
            $browser->visit('/localization')
                ->assertPathIs('/login');
        });
    }

    public function testVisitPageWithLogin(){

        $this->browse(function (Browser $browser){
            $browser->loginAs(1)
                ->visit('/localization')
                ->assertSee('Language List');
        });
    }

    public function testAddNewLanguage(){
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser
                ->click('#main-content > section > div > div > div.col-12 > div > div > ul > li > a')
                ->whenAvailable('#language_add', function ($modal){
                    $modal
                        ->type('#language_addForm > div > div:nth-child(1) > div > input', 'Test')
                        ->type('#language_addForm > div > div:nth-child(2) > div > input', 'te')
                        ->type('#language_addForm > div > div:nth-child(3) > div > input', 'Test')
                        ->click('#language_addForm > div > div.col-lg-12.text-center > div > button');

                })
                ->waitFor('.toast-message')
                ->assertSee('Language Added Successfully');
        });
    }


    public function testEditLanguage(){
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(6) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(6) > div > div > a:nth-child(1)')

                ->whenAvailable('#Item_Edit', function ($modal){
                    $modal

                        ->click('#languageEditForm > div > div.col-lg-12.text-center > div > button');

                })
                ->waitFor('.toast-message')
                ->assertSee('Language Updated Successfully');
        });
    }

    public function testTranslationLanguage(){
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(6) > div > button')
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(6) > div > div > a:nth-child(2)')
                ->assertSee('English Translation')
                ->pause(5000)
                ->click('#translate_modal > form > div:nth-child(2) > div > div > button')

                ->waitFor('.toast-message')
                ->assertSee('Operation Successfully done');
        });
    }


    public function testChangeLanguageStatus(){
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(5) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Updated Successfully')
            ->pause(1000)
                ->click('#DataTables_Table_0 > tbody > tr:nth-child(1) > td:nth-child(5) > label > div')
                ->waitFor('.toast-message')
                ->assertSee('Updated Successfully');
        });
    }

    public function testChangeLanguageFromTopOfHeader(){
        $this->testVisitPageWithLogin();
        $this->browse(function (Browser $browser){
            $browser
                ->click('#main-content > div.container-fluid.no-gutters > div > div > div > div:nth-child(5) > div > div:nth-child(5)')
                ->click('#main-content > div.container-fluid.no-gutters > div > div > div > div:nth-child(5) > div > div.nice-select.nice_Select.bgLess.mb-0.open > ul > li')
                ->pause(3000)

                ->assertPathIs('/localization')
            ;
        });
    }

}
