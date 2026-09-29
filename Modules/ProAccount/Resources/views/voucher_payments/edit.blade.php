@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.update_voucher_payment_info'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{trans("account.update_voucher_payment_info")}} - {{ $journal->txn_id }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <form action="{{ route('vouchers.update', $journal->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.date')}} *</label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="position_relative">
                                                        <input placeholder="Date" class="primary_input_field primary-input custom-date form-control" id="date" type="text" name="date" value="{{ date('m/d/Y', strtotime($journal->date)) }}" autocomplete="off" required>
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
                                @php
                                    $credit_account = $journal->transactions->where('type', 'Cr')->first();
                                    $dedit_account = $journal->transactions->where('type', 'Dr')->first();
                                @endphp

                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.partner_account')}} *</label>
                                        <div class="sub_account_below_div">
                                            <select class="select2 mb-15 payment_to_sub" name="credit_sub_account_id" id="credit_sub_account_id">
                                                <option value="{{ $dedit_account->sub_leadger_id }}" selected>{{ $dedit_account->sub_leadger->name }}</option>
                                            </select>
                                        </div>
                                        <span class="text-danger">{{$errors->first('credit_sub_account_id')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{trans('account.credit_account')}} *</label>
                                        <input class="primary_input_field leadger_name" name="leadger_name" id="leadger_name" value="{{ $dedit_account->leadger->name }}" readonly>
                                        <span class="text-danger">{{$errors->first('leadger_name')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.due_invoice_list')}}</label>
                                        <div class="due_invoice_list_below_div">
                                            <select class="select2 mb-15 due_invoice_list" name="due_invoice_list" id="due_invoice_list">
                                                <option value="0">{{trans('account.Select one')}}</option>
                                                @if ($journal->referable_type != null)
                                                <option value="{{$journal->referable->id}}" selected>{{$journal->referable->invoice_no}}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <span class="text-danger">{{$errors->first('due_invoice_list')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.discount_percentage')}} (%)</label>
                                        <input class="primary_input_field discount_percentage" name="discount_percentage" id="discount_percentage" value="{{ ($journal->invoice_payments && $journal->invoice_payments->amount_after_discount > 0) ? ($journal->invoice_payments->amount - $journal->invoice_payments->amount_after_discount) * 100 / $journal->invoice_payments->amount : 0 }}">
                                        <span class="text-danger">{{$errors->first('discount_percentage')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.discount_amount')}} </label>
                                        <input class="primary_input_field discount_amount" name="discount_amount" id="discount_amount" value="{{ ($journal->invoice_payments && $journal->invoice_payments->amount_after_discount > 0) ? $journal->invoice_payments->amount - $journal->invoice_payments->amount_after_discount : 0 }}" readonly>
                                        <span class="text-danger">{{$errors->first('discount_amount')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{trans('account.narration')}} </label>
                                        <textarea class="primary_textarea height_112 narration" placeholder="{{trans('account.narration')}}" name="narration" spellcheck="false">{{ $journal->narration }}</textarea>
                                        <span class="text-danger">{{$errors->first('narration')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-4 payment_from_div">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.credit_account')}} *</label>
                                        <div class="payment_from_account">
                                            <select class="select2 mb-15 debit_account_id" data-row="1" name="debit_account_id" id="debit_account_id" required>
                                                <option value="{{ $credit_account->leadger_id }}" selected>{{ $credit_account->leadger->name }}</option>
                                            </select>
                                        </div>
                                        <span class="text-danger">{{$errors->first('debit_account_id')}}</span>
                                    </div>
                                </div>
                                @if (Settings('use_cash_flow_in_accounting') == 1)
                                    <div class="col-lg-4">
                                        <div class="primary_input mb-15 ">
                                            <label class="primary_input_label" for="">{{trans('account.cash_flow')}}</label>
                                            <div class="cash_flow_account_upper_div">
                                                <select class="select2 mb-15 cash_flow_account" name="cash_flow_account[]">
                                                    @if ($credit_account->cash_flow_detail)
                                                        <option value="{{ $credit_account->cash_flow_detail->cash_flow_account_id }}" selected>{{ $credit_account->cash_flow_detail->cash_flow_account->name }}</option>
                                                    @else
                                                        <option value="0">{{trans('account.Select one')}}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <span class="text-danger">{{$errors->first('cash_flow_account.*')}}</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-lg-4">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{trans('account.amount')}} *</label>
                                        <input class="primary_input_field sub_amount" name="sub_amount[]" id="sub_amount" placeholder="Amount" type="number" min="0" step="0.01" value="{{ $credit_account->amount }}">
                                        <span class="text-danger">{{$errors->first('sub_amount.*')}}</span>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                   <div class="primary_input mb-15">
                                      <label class="primary_input_label red_input" for="">{{ trans("account.discount_is_only_for_invoice_purpose.if_you_try_this_for_non_invoice_related_transaction_discount_will_not_be_traced_by_system!!!") }}</label>
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
        <input type="hidden" value="{{route('voucher.get_accounts_for_select_option')}}" id="get_accounts_for_payment">
        <input type="hidden" value="{{route('sub_leadger.get_data_list_for_select')}}" id="sub_leadger_by_leadger">
        <input type="hidden" value="{{route('leadger.get_leadger_for_select')}}" id="leadger_list_select_option">
        <input type="hidden" value="{{route('cash_flow_account.get_data_list_for_select')}}" id="cash_flow_list_select_option">
        <input type="hidden" value="{{route('sub_leadger.parent_account_by_id', ":id")}}" id="get_parent_account">
        <input type="hidden" value="{{route('leadger.cost_center')}}" id="cost_center_url">
        <input type="hidden" value="{{route('leadger.store')}}" id="chart_account_form_url">
        <input type="hidden" value="{{route('sub_leadger.store')}}" id="create_contact_form_url">
    </section>
@endsection

@push("scripts")
    <script src="{{ Module::asset('account:voucher_rec_create.js') }}"></script>
@endpush
