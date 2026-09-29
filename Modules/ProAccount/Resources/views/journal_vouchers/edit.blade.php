@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.update_journal_info'))
@push('css')
    <style>
    .table thead th {
        border-bottom: 2px solid var(--border_color);
        padding: 5px;
        text-align: center;
        font-weight: 600;
        white-space: nowrap;
        border-top: 2px solid var(--border_color) !important;
    }
    .table tbody td {
        padding: 10px 5px 5px 5px !important;
        font-weight: 500 !important;
    }
    .table_details tbody td {
        padding: 0px 1px 1px 1px !important;
        font-weight: 500 !important;
    }
    .nowrap {
        white-space: nowrap;
    }
    .f_10 {
        font-size: 11px;
    }
    .color_input {
        color: #ae36e5;
        font-size: 14px;
    }
    </style>
@endpush
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
                            <h3 class="mb-0 mr-30">{{trans('account.update_journal_info')}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <form class="journal_update_form" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.date')}} *</label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="position_relative">
                                                        <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="date" type="text" name="date" value="{{ date('m/d/Y', strtotime($journal->date)) }}" autocomplete="off" required>
                                                        <div class="custom_datepicker_design"></div>
                                                    </div>
                                                </div>
                                                <button class="date-icon" type="button">
                                                    <i class="ti-calendar"></i>
                                                </button>
                                            </div>
                                            <span class="text-danger">{{$errors->first('date')}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{trans('account.journal_type')}} *</label>
                                        <select class="primary_select mb-25 journal_type" name="journal_type" id="journal_type" required>
                                            <option value="misc" @if ($journal->type == "misc") selected @endif>{{trans('account.miscellaneous')}}</option>
                                            <option value="cash" @if ($journal->type == "cash") selected @endif>{{trans('account.cash')}}</option>
                                            <option value="bank" @if ($journal->type == "bank") selected @endif>{{trans('account.bank')}}</option>
                                        </select>
                                        <span class="text-danger" id="type_error"></span>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.is_cashflow_journal')}} *</label>
                                        <ul class="permission_list sms_list">
                                            <li>
                                                <label class="primary_checkbox d-flex mr-12 ">
                                                    <input name="is_cashflow_journal" type="radio" id="is_cashflow_journal_yes" value="yes" class="is_cashflow_journal" @if ($journal->is_cash_flow_journal == 1) checked @endif>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <p>{{ trans('account.yes') }}</p>
                                            </li>
                                            <li>
                                                <label class="primary_checkbox d-flex mr-12 ">
                                                    <input name="is_cashflow_journal" type="radio" id="is_cashflow_journal_no" value="no" class="is_cashflow_journal" @if ($journal->is_cash_flow_journal == 0) checked @endif>
                                                    <span class="checkmark"></span>
                                                </label>
                                                <p>{{ trans('account.no') }}</p>
                                            </li>
                                        </ul>
                                        <span class="text-danger">{{$errors->first('is_cashflow_journal')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{trans('account.narration')}} </label>
                                        <textarea class="primary_textarea height_112 narration" placeholder="{{trans('account.narration')}}" name="narration_voucher" spellcheck="false">{{ $journal->narration }}</textarea>
                                        <span class="text-danger">{{$errors->first('narration_voucher')}}</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="rowId" value="{{$journal->id}}">
                            <label class="h1 primary_input_label color_input">{{trans('account.journal_details')}}</label>
                            <label class="h1 primary_input_label red_input d-none text-right">{{trans('account.cash_flow_account_is_mandatory_for_cash_flow_journal')}}</label>
                            <hr class="dashed">
                            <div class="entry_row_div">
                                @foreach ($journal->transactions as $key => $transaction)
                                    <div class="row new_added_row">
                                        <div class="{{$row_width_leadger}} upper_account_div">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{trans('account.select_account')}} *</label>
                                                <select class="select2 mb-15 account_id" name="account_id[]" data-row="{{ $key+1 }}" required>
                                                    <option value="{{$transaction->leadger_id}}" selected>{{$transaction->leadger->code}} ({{$transaction->leadger->name}})</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-2">
                                            <div class="primary_input mb-15 ">
                                                <label class="primary_input_label" for="">{{trans('account.partner')}}</label>
                                                <div class="sub_account_upper_div">
                                                    <select class="select2 mb-15 sub_account_id" name="sub_account_id[]" data-rows="{{ $key+1 }}">
                                                        @if ($transaction->sub_leadger_id == 0)
                                                            <option value="0">{{trans('account.Select one')}}</option>
                                                        @else
                                                            <option value="{{$transaction->sub_leadger_id}}" selected>{{$transaction->sub_leadger->code}} ({{$transaction->sub_leadger->name}})</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @if (Settings('use_cash_flow_in_accounting') == 1)
                                            <div class="{{ $row_width_cash_flow }}">
                                                <div class="primary_input mb-15 ">
                                                    <label class="primary_input_label" for="">{{trans('account.cash_flow')}}</label>
                                                    <div class="cash_flow_account_upper_div">
                                                        <select class="select2 mb-15 cash_flow_account" name="cash_flow_account[]">
                                                            @if ($transaction->cash_flow_detail == null)
                                                                <option value="0">{{trans('account.Select one')}}</option>
                                                            @else
                                                                <option value="{{$transaction->cash_flow_detail->cash_flow_account_id}}" selected>{{$transaction->cash_flow_detail->cash_flow_account->code}} ({{$transaction->cash_flow_detail->cash_flow_account->name}})</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-lg-2">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{trans('account.narration')}}</label>
                                                <input class="primary_input_field" name="narration[]" id="narration" value="{{ $transaction->narration }}" type="text">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 debit_amount_div">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{trans('account.debit')}} *</label>
                                                <input class="primary_input_field debit_amount" name="debit_amount[]" id="debit_amount" value="{{ ($transaction->type == "Dr") ? $transaction->amount : 0}}" type="number" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 credit_amount_div">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{trans('account.credit')}} *</label>
                                                <input class="primary_input_field credit_amount" name="credit_amount[]" id="credit_amount" value="{{ ($transaction->type == "Cr") ? $transaction->amount : 0}}" type="number" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-lg-1">
                                            <div class="primary_input mb-15 action_div">
                                                <label class="primary_input_label" for=""> {{trans('account.action')}} </label>
                                                <a class="primary-btn btn-sm delete_new_row"><i class="fas fa-trash-alt required_mark2 f_s_13"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
        @include('proaccount::leadger_accounts.create')
        @include('proaccount::income.create_contact')
    </section>

    <input type="hidden" value="{{Settings('currency_symbol')}}" id="currency_symbol">
    <input type="hidden" value="{{route('journal.index')}}" id="index_url">
    <input type="hidden" value="{{route('journal.update', ":id")}}" id="journal_update_url">
    <input type="hidden" value="{{route('sub_leadger.get_data_list_for_select')}}" id="sub_leadger_by_leadger">
    <input type="hidden" value="{{route('leadger.get_leadger_for_select')}}" id="leadger_list_select_option">
    <input type="hidden" value="{{route('cash_flow_account.get_data_list_for_select')}}" id="cash_flow_list_select_option">
    <input type="hidden" value="{{ route("leadger.get_table_row_data") }}" id="get_tbl_row_data">
    <input type="hidden" value="{{ route("journal.add_new_line") }}" id="add_new_entry">
    <input type="hidden" value="{{route('sub_leadger.store')}}" id="create_contact_form_url">
    <input type="hidden" value="{{route('leadger.store')}}" id="chart_account_form_url">
    <input type="hidden" value="{{route('leadger.cost_center')}}" id="cost_center_url">
@endsection

@push("scripts")
    <script src="{{ Module::asset('account:journal_entry.js') }}"></script>
    <script src="{{ Module::asset('account:instant_create.js') }}"></script>
@endpush
