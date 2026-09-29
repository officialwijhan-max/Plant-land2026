<?php

namespace App\Traits;
use Modules\Account\Entities\ChartAccount;
use Modules\ProAccount\Entities\Leadger;
use Modules\ProAccount\Entities\SubLeadger;
use Modules\Setup\Entities\Tax;

trait Accounts
{
   
    public function defaultSalesAccount()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Settings('default_sales_account');
        } else {
            return ChartAccount::where('code', '04-15')->first()->id;
        }
    }

    public function defaultSalesReturnAccount()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Settings('default_sales_return_account');
        } else {
            return ChartAccount::where('code', '03-23')->first();
        }
    }

    public function defaultProductTaxAccount()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Settings('default_product_tax_account');
        } else {
            return ChartAccount::where('code', '02-12-13')->first()->id;
        }
    }

    public function othersTaxAccountByTaxId($id)
    {
        if(Tax::findOrFail($id)->account){
            return Tax::findOrFail($id)->account->id;
        }else{
            return $this->defaultProductTaxAccount();
        }
    }

    public function shippingOrOthersChargeIncome()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Settings('shipping_and_other_charge_income');
        } else {
            return ChartAccount::where('code', '04-16-28')->first()->id;
        }
    }

    public function shippingOrOthersChargeExpense()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Settings('shipping_and_other_charge_expense');
        } else {
            return ChartAccount::where('code', '01-27')->first()->id;
        }
    }

    public function defaultOtherPurchaseTaxAccount()
    {
        return ChartAccount::where('code', '01-27')->first()->id;
    }

    public function defaultPurchaseAccount()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Settings('default_purchase_account');
        } else {
            return ChartAccount::where('code', '01-07')->first()->id;
        }
    }

    public function defaultPurchaseReturnAccount()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Settings('default_purchase_account');
        } else {
            return ChartAccount::where('code', '04-24')->first();
        }   
    }

    public function defaultCostofGoodsSoldAccount()
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Settings('default_cost_of_goods_sold_account');
        } else {
            return ChartAccount::where('code', '03-19')->first()->id;
        }   
    }

    public function defaultWalkInCustomerAccount()
    {
        return ChartAccount::where('code', '01-05-25')->first();
    }

    public function inventoryBankAccount($account_id)
    {
        return ChartAccount::findOrFail($account_id)->id;
    }

    public function AccountFind($contactable_id, $contactable_type)
    {
        return ChartAccount::where('contactable_id', $contactable_id)->where('contactable_type', $contactable_type)->latest()->first();
    }

    public function PartnerAccountFind($contactable_id, $contactable_type)
    {
        return SubLeadger::where('morphable_type', $contactable_type)->where('morphable_id', $contactable_id)->first();
    }

    public function GetAccountId($contactable_id, $contactable_type)
    {
        if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") {
            return Leadger::where('morphable_type', $contactable_type)->where('morphable_id', $contactable_id)->first();
        } else {
            return ChartAccount::where('contactable_id', $contactable_id)->where('contactable_type', $contactable_type)->first()->id;
        }
    }

    public function defaultRetailEarningProfitAccount()
    {
        return  ChartAccount::where('code','02-14')->first()->id;
    }
}
