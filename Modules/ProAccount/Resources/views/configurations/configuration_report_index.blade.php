@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.report_configuration'))
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
        <div class="white_box_50px box_shadow_white">
            <form action="{{ route('account.configuration_update_for_approval') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="box_header common_table_header">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px config_title">{{ trans('account.report_configuration') }}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.direct_income_leadger')}} *</label>
                            <select class="primary_select mb-15 direct_income_leadger" name="direct_income_leadger" id="direct_income_leadger" required>
                                <option value="0">{{trans('account.Select one')}}</option>
                                @foreach ($accounts->where('type', 4) as $key => $account)
                                    <option value="{{ $account->id }}" @if (Settings('direct_income_leadger') == $account->id) selected @endif>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('direct_income_leadger')}}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.in_direct_income_leadger')}} *</label>
                            <select class="primary_select mb-15 in_direct_income_leadger" name="in_direct_income_leadger" id="in_direct_income_leadger" required>
                                <option value="0">{{trans('account.Select one')}}</option>
                                @foreach ($accounts->where('type', 4) as $key => $account)
                                    <option value="{{ $account->id }}" @if (Settings('in_direct_income_leadger') == $account->id) selected @endif>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('in_direct_income_leadger')}}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.direct_expense_leadger')}} *</label>
                            <select class="primary_select mb-15 direct_expense_leadger" name="direct_expense_leadger" id="direct_expense_leadger" required>
                                <option value="0">{{trans('account.Select one')}}</option>
                                @foreach ($accounts->where('type', 3) as $key => $account)
                                    <option value="{{ $account->id }}" @if (Settings('direct_expense_leadger') == $account->id) selected @endif>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('direct_expense_leadger')}}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.in_direct_expense_leadger')}} *</label>
                            <select class="primary_select mb-15 in_direct_expense_leadger" name="in_direct_expense_leadger" id="in_direct_expense_leadger" required>
                                <option value="0">{{trans('account.Select one')}}</option>
                                @foreach ($accounts->where('type', 3) as $key => $account)
                                    <option value="{{ $account->id }}" @if (Settings('in_direct_expense_leadger') == $account->id) selected @endif>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('in_direct_expense_leadger')}}</span>
                        </div>
                    </div>
                </div>
                <div class="box_header common_table_header mt-4">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px config_title">{{ trans('account.financial_year_configuration') }}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.income_summary_debit_leadger')}} ({{ __('account.Expense') }}) *</label>
                            <select class="primary_select mb-15 income_summary_debit_leadger" name="income_summary_debit_leadger" id="income_summary_debit_leadger" required>
                                <option value="0">{{trans('account.Select one')}}</option>
                                @foreach ($transaction_accounts->where('type', 3) as $key => $transaction_account)
                                    <option value="{{ $transaction_account->id }}" @if (Settings('income_summary_debit_leadger') == $transaction_account->id) selected @endif>{{ $transaction_account->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('income_summary_debit_leadger')}}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.retail_earning_leadger')}} ({{ trans('account.liability') }}) *</label>
                            <select class="primary_select mb-15 retail_earning_leadger" name="retail_earning_leadger" id="retail_earning_leadger" required>
                                <option value="0">{{trans('account.Select one')}}</option>
                                @foreach ($transaction_accounts->where('type', 2) as $key => $transaction_account)
                                    <option value="{{ $transaction_account->id }}" @if (Settings('retail_earning_leadger') == $transaction_account->id) selected @endif>{{ $transaction_account->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('retail_earning_leadger')}}</span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.company_tax_payable')}} ({{ trans('account.liability') }}) *</label>
                            <select class="primary_select mb-15 company_tax_leadger" name="company_tax_leadger" id="company_tax_leadger" required>
                                <option value="0">{{trans('account.Select one')}}</option>
                                @foreach ($transaction_accounts->where('type', 2) as $key => $transaction_account)
                                    <option value="{{ $transaction_account->id }}" @if (Settings('company_tax_leadger') == $transaction_account->id) selected @endif>{{ $transaction_account->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('company_tax_leadger')}}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.company_income_tax')}} ({{ __('account.Expense') }}) *</label>
                            <select class="primary_select mb-15 company_income_tax" name="company_income_tax" id="company_income_tax" required>
                                <option value="0">{{trans('account.Select one')}}</option>
                                @foreach ($transaction_accounts->where('type', 3) as $key => $transaction_account)
                                    <option value="{{ $transaction_account->id }}" @if (Settings('company_income_tax') == $transaction_account->id) selected @endif>{{ $transaction_account->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{$errors->first('company_income_tax')}}</span>
                        </div>
                    </div>
                </div>

                <div class="box_header common_table_header mt-4">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px config_title">{{ trans('account.tax_configuration') }}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="primary_input mb-15">
                            <label class="primary_input_label" for="">{{trans('account.company_tax')}} (%) *</label>
                            <input class="primary_input_field company_tax" name="company_tax" id="company_tax" value="{{ Settings('company_tax') }}" type="number" min="0" max="100" step="0.01">
                            <span class="text-danger">{{$errors->first('company_tax')}}</span>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 submit_btn text-center">
                        <button class="primary_btn_2 save_btn" type="submit"> <i class="ti-check"></i> {{ trans('account.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@push("scripts")
    <script src="{{ Module::asset('account:configurations.js') }}"></script>
@endpush
