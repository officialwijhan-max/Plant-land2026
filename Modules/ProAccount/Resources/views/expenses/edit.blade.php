@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.expense_edit'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{trans('account.expense_edit')}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <form class="journal_update_form" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.date')}} *</label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="position_relative">
                                                        <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="date" type="text" name="date" value="{{ date('m/d/Y', strtotime($row->date)) }}" autocomplete="off" required>
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
                                @php
                                    $credit_account = $row->transactions->where('type', 'Cr')->first();
                                    $dedit_account = $row->transactions->where('type', 'Dr')->first();
                                @endphp

                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ __('account.Payment from Account') }} *</label>
                                        <div class="sub_account_below_div">
                                            <select class="select2 mb-15 credit_account_id" name="credit_account_id" id="credit_account_id">
                                                <option value="{{ $credit_account->leadger_id }}" selected>{{ $credit_account->leadger->name }}</option>
                                            </select>
                                        </div>
                                        <span class="text-danger">{{$errors->first('')}}</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="rowId" value="{{$row->id}}">
                            <hr class="dashed">
                            <div class="entry_row_div">
                                @foreach ($row->transactions->where('type', 'Dr') as $key => $transaction)
                                    <div class="row new_added_row">
                                        <div class="col-lg-3 upper_account_div">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{trans('account.select_account')}} *</label>
                                                <select class="select2 mb-15 account_id" data-row="{{ $key+1 }}" name="account_id[]" required>
                                                    <option value="{{$transaction->leadger_id}}" selected>{{$transaction->leadger->code}} ({{$transaction->leadger->name}})</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{trans('account.narration')}}</label>
                                                <input class="primary_input_field" name="narration[]" id="narration" value="{{ $transaction->narration }}" type="text">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 credit_amount_div">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{trans('account.credit')}} *</label>
                                                <input class="primary_input_field debit_amount" name="debit_amount[]" id="debit_amount" value="{{ $transaction->amount }}" type="number" min="0" step="0.01">
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{ trans('contact.Customer') }} / {{ trans('contact.Supplier') }}</label>
                                                <div class="sub_account_below_div">
                                                    <select class="select2 mb-15 sub_account_id" data-rows="{{ $key+1 }}" name="sub_account_id[]">
                                                        @if ($transaction->sub_leadger_id && $transaction->sub_leadger_id > 0)
                                                            <option value="{{$transaction->sub_leadger_id}}" selected>{{$transaction->sub_leadger->code}} ({{$transaction->sub_leadger->name}})</option>
                                                        @else
                                                            <option value="0">{{trans('account.Select one')}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <span class="text-danger">{{$errors->first('sub_account_id')}}</span>
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
    </section>

    @include('proaccount::leadger_accounts.create')
    @include('proaccount::income.create_contact')
    <input type="hidden" value="{{Settings('currency_symbol')}}" id="currency_symbol">
    <input type="hidden" value="{{route('voucher.get_accounts_for_select_option')}}" id="get_accounts_for_payment">
    <input type="hidden" value="{{route('pro-expenses.index')}}" id="index_url">
    <input type="hidden" value="{{route('pro-expenses.update', ":id")}}" id="journal_update_url">
    <input type="hidden" value="{{route('pro-expenses.create')}}" id="create_url">
    <input type="hidden" value="{{route('leadger.get_leadger_for_select')}}" id="leadger_list_select_option">
    <input type="hidden" value="{{ route("pro-income.add_new_line") }}" id="add_new_entry">
    <input type="hidden" value="{{route('sub_leadger.get_data_list_for_select')}}" id="sub_leadger_by_leadger">
    <input type="hidden" value="{{route('sub_leadger.store')}}" id="create_contact_form_url">
    <input type="hidden" value="{{route('leadger.store')}}" id="chart_account_form_url">
    <input type="hidden" value="{{route('leadger.cost_center')}}" id="cost_center_url">
@endsection

@push("scripts")
    <script src="{{ Module::asset('account:income_entry.js') }}"></script>
    <script src="{{ Module::asset('account:instant_create.js') }}"></script>
@endpush
