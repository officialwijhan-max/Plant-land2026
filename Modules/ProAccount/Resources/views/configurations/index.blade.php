@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.accounting_configuration'))
@push('css')
    <style>
        .config_title {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: 17px !important;
            line-height: 10px !important;
        }
    </style>
@endpush
@section('mainContent')
<section class="admin-visitor-area">
    <div class="container-fluid p-0 mt-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="box_header common_table_header">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.accounting_configuration') }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="white_box_50px box_shadow_white">
                <form action="{{ route('account.configuration_update_for_approval') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.recievable_account')}} *</label>
                                    <select class="primary_select mb-15 account_recievable" name="account_recievable" id="account_recievable" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('account_recievable') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('account_recievable')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.payable_account')}} *</label>
                                    <select class="primary_select mb-15 account_payable" name="account_payable" id="account_payable" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('account_payable') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('account_payable')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.leadger_account_for_employee')}} *</label>
                                    <select class="primary_select mb-15 leadger_account_for_employee" name="leadger_account_for_employee" id="leadger_account_for_employee" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 2) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('leadger_account_for_employee') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('leadger_account_for_employee')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.default_expense_account')}} *</label>
                                    <select class="primary_select mb-15 default_expense_account" name="default_expense_account" id="default_expense_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 3) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_expense_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_expense_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.default_income_account')}} *</label>
                                    <select class="primary_select mb-15 default_income_account" name="default_income_account" id="default_income_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 4) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_income_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_income_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.default_salary_expense_account')}} *</label>
                                    <select class="primary_select mb-15 default_salary_expense_account" name="default_salary_expense_account" id="default_salary_expense_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 3) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_salary_expense_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_salary_expense_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.advance_loan_and_accured_salary_account')}} *</label>
                                    <select class="primary_select mb-15 advance_loan_and_accured_salary_account" name="advance_loan_and_accured_salary_account" id="advance_loan_and_accured_salary_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 1) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('advance_loan_and_accured_salary_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('advance_loan_and_accured_salary_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.employee_tax_ledger')}} *</label>
                                    <select class="primary_select mb-15 employee_tax_ledger" name="employee_tax_ledger" id="employee_tax_ledger" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 2) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('employee_tax_ledger') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('employee_tax_ledger')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.default_sales_account')}} *</label>
                                    <select class="primary_select mb-15 default_sales_account" name="default_sales_account" id="default_sales_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_sales_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_sales_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.default_sales_return_account')}} *</label>
                                    <select class="primary_select mb-15 default_sales_return_account" name="default_sales_return_account" id="default_sales_return_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_sales_return_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_sales_return_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.inventory')}} / {{ trans('account.purchase') }} *</label>
                                    <select class="primary_select mb-15 default_purchase_account" name="default_purchase_account" id="default_purchase_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_purchase_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_purchase_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.default_cost_of_goods_sold_account')}} *</label>
                                    <select class="primary_select mb-15 default_cost_of_goods_sold_account" name="default_cost_of_goods_sold_account" id="default_cost_of_goods_sold_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_cost_of_goods_sold_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_cost_of_goods_sold_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.default_capital_account')}} (CR) *</label>
                                    <select class="primary_select mb-15 default_capital_account" name="default_capital_account" id="default_capital_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 2) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_capital_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_capital_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.shipping_and_other_charge_expense')}} *</label>
                                    <select class="primary_select mb-15 shipping_and_other_charge_expense" name="shipping_and_other_charge_expense" id="shipping_and_other_charge_expense" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 3) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('shipping_and_other_charge_expense') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('shipping_and_other_charge_expense')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.shipping_and_other_charge_income')}} *</label>
                                    <select class="primary_select mb-15 shipping_and_other_charge_income" name="shipping_and_other_charge_income" id="shipping_and_other_charge_income" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 4) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('shipping_and_other_charge_income') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('shipping_and_other_charge_income')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.default_product_tax_account')}} *</label>
                                    <select class="primary_select mb-15 default_product_tax_account" name="default_product_tax_account" id="default_product_tax_account" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 2) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('default_product_tax_account') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('default_product_tax_account')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.purchase_discount_recieve_at_payment_time') }} *</label>
                                    <select class="primary_select mb-15 purchase_discount_recieve_at_payment_time" name="purchase_discount_recieve_at_payment_time" id="purchase_discount_recieve_at_payment_time" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 4) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('purchase_discount_recieve_at_payment_time') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('purchase_discount_recieve_at_payment_time')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.sale_discount_at_recieve_time') }} *</label>
                                    <select class="primary_select mb-15 sale_discount_at_recieve_time" name="sale_discount_at_recieve_time" id="sale_discount_at_recieve_time" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 3) as $key => $account)
                                            <option value="{{ $account->id }}"@if (Settings('sale_discount_at_recieve_time') == $account->id) selected @endif>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('sale_discount_at_recieve_time')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.opening_balance_quity_account') }} *</label>
                                    <select class="primary_select mb-15 opening_balance_equity" name="opening_balance_equity" id="opening_balance_equity" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 2) as $key => $account)
                                            <option value="{{ $account->id }}" @if (Settings('opening_balance_equity') == $account->id) selected @endif>@if (in_array($account->id, app('equity_account')['equity_account'])) ({{ trans('account.equity') }}) @endif{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('opening_balance_equity')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.stock_adjustment_loss') }} *</label>
                                    <select class="primary_select mb-15 stock_adjustment_loss" name="stock_adjustment_loss" id="stock_adjustment_loss" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 3) as $key => $account)
                                            <option value="{{ $account->id }}" @if (Settings('stock_adjustment_loss') == $account->id) selected @endif>@if (in_array($account->id, app('equity_account')['equity_account'])) ({{ trans('account.equity') }}) @endif{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('stock_adjustment_loss')}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.stock_adjustment_income') }} *</label>
                                    <select class="primary_select mb-15 stock_adjustment_income" name="stock_adjustment_income" id="stock_adjustment_income" required>
                                        <option value="0">{{trans('account.Select one')}}</option>
                                        @foreach ($accounts->where('type', 4) as $key => $account)
                                            <option value="{{ $account->id }}" @if (Settings('stock_adjustment_income') == $account->id) selected @endif>@if (in_array($account->id, app('equity_account')['equity_account'])) ({{ trans('account.equity') }}) @endif{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('stock_adjustment_income')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 submit_btn text-center">
                                <button class="primary_btn_2 save_btn" type="submit"> <i class="ti-check"></i> {{ trans('account.save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
