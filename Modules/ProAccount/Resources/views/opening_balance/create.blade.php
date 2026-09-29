@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.add_openning_balance'))

@section('mainContent')
@php
$row_width_leadger = "col-lg-3";
$row_width_cash_flow = "col-lg-2";
if (Settings('use_cash_flow_in_accounting') == 0) {
    $row_width_leadger = "col-lg-5";
}
@endphp
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{ trans('account.add_openning_balance') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <!-- Prefix  -->
                        <form class="journal_create_form" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.date')}} *</label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="position_relative">
                                                        <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date custom-date form-control" id="date" type="text" name="date" value="{{date('m/d/Y')}}" autocomplete="off" required>
                                                        <div class="custom_datepicker_design"></div>
                                                    </div>
                                                </div>
                                                <button class="date-icon" type="button">
                                                    <i class="ti-calendar"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ __('account.financial_years') }}*</label>
                                        <div class="sub_account_below_div">
                                            <select class="primary_select mb-15 financial_year_id" name="financial_year_id" id="financial_year_id">
                                                @foreach ($financial_years as $financial_year)
                                                    <option value="{{ $financial_year->id }}">{{ showDate($financial_year->start_date) }} - {{ ($financial_year->end_date) ? showDate($financial_year->end_date) : "Current" }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="text-danger">{{$errors->first('financial_year_id')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{trans('account.narration')}} </label>
                                        <textarea class="primary_textarea height_112 narration" placeholder="{{trans('account.narration')}}" name="narration_voucher" spellcheck="false"></textarea>
                                        <span class="text-danger">{{$errors->first('narration_voucher')}}</span>
                                    </div>
                                </div>
                            </div>
                            <hr class="dashed">
                            <div class="entry_row_div">
                                <div class="row new_added_row">
                                    <div class="{{$row_width_leadger}} upper_account_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{trans('account.select_account')}} *</label>
                                            <select class="select2 mb-15 account_id" name="account_id[]" data-row="1" required>
                                                <option value="0">{{ trans('account.Select one') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15 ">
                                            <label class="primary_input_label" for="">{{trans('account.partner')}}</label>
                                            <div class="sub_account_upper_div">
                                                <select class="select2 mb-15 sub_account_id" data-rows="1" name="sub_account_id[]">
                                                    <option value="0">{{ trans('account.Select one') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{trans('account.narration')}}</label>
                                            <input class="primary_input_field" name="narration[]" id="narration" value="" type="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-1 debit_amount_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{trans('account.debit')}} *</label>
                                            <input class="primary_input_field debit_amount" name="debit_amount[]" id="debit_amount" value="0" type="number" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-lg-1 credit_amount_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{trans('account.credit')}} *</label>
                                            <input class="primary_input_field credit_amount" name="credit_amount[]" id="credit_amount" value="0" type="number" min="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <div class="primary_input mb-15 action_div">
                                            <label class="primary_input_label" for=""> {{trans('account.action')}} </label>
                                            <a class="primary-btn btn-sm delete_new_row"><i class="fas fa-trash-alt required_mark2 f_s_13"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="dashed">
                            <div class="row">
                                <div class="col-lg-9 text-right">
                                    <div class="primary_input mb-15">
                                        <label class="h1 primary_input_label color_input" for="">{{trans('account.total')}} :</label>
                                    </div>
                                </div>
                                <div class="col-lg-1 text-center">
                                    <div class="primary_input mb-15">
                                        <label class="h1 primary_input_label color_input total_debit_amount" for="">{{single_price(0)}}</label>
                                    </div>
                                </div>
                                <div class="col-lg-1 text-center">
                                    <div class="primary_input mb-15">
                                        <label class="h1 primary_input_label color_input total_credit_amount" for="">{{single_price(0)}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-1">
                                    <div class="primary_input action_div">
                                        <a class="primary-btn radius_30px mr-10 fix-gr-bg" id="add_new_line"><i class="ti-plus"></i>{{trans('account.add_line')}}</a>
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <div class="primary_input action_div">
                                        <a class="primary-btn radius_30px mr-10 fix-gr-bg" id="refresh_btn"><i class="ti-reload"></i>{{trans('account.refresh')}}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-20 text-center">
                                <div class="col-xl-12">
                                    <button class="primary-btn fix-gr-bg submit_button_form save_and_close_button mt-3">
                                        <i class="ti-check"></i>{{__('common.Save')}}
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" id="save_and_close_button" class="save_and_close_button" name="save_and_close_button" value="only_save">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('proaccount::leadger_accounts.create')
    @include('proaccount::income.create_contact')
    <input type="hidden" value="{{Settings('currency_symbol')}}" id="currency_symbol">
    <input type="hidden" value="{{route('voucher.get_accounts_for_select_option')}}" id="get_accounts_for_payment">
    <input type="hidden" value="{{route('pro-opening-balance.store')}}" id="journal_store_url">
    <input type="hidden" value="{{route('pro-opening-balance.create')}}" id="create_url">
    <input type="hidden" value="{{route('leadger.get_leadger_for_select')}}" id="leadger_list_select_option">
    <input type="hidden" value="{{ route("pro-opening-balance.add_new_line") }}" id="add_new_entry">
    <input type="hidden" value="{{route('sub_leadger.get_data_list_for_select')}}" id="sub_leadger_by_leadger">
    <input type="hidden" value="{{route('sub_leadger.store')}}" id="create_contact_form_url">
    <input type="hidden" value="{{route('leadger.store')}}" id="chart_account_form_url">
    <input type="hidden" value="{{route('leadger.cost_center')}}" id="cost_center_url">
@endsection

@push("scripts")
<script src="{{ Module::asset('account:opening_entry.js') }}"></script>
<script src="{{ Module::asset('account:instant_create.js') }}"></script>
@endpush
