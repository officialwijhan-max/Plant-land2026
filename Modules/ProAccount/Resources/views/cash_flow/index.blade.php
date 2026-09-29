@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings('site_title') .' | '. trans('account.cash_flow'))
@section('mainContent')
    @php
        $balanceDebit = 0;
        $balanceCredit = 0;
    @endphp
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="white_box_50px p-5 mb-20">
                <form class="" action="{{ route('vouchers.cash_flow') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="primary_input mt-3 mb-15">
                                <label class="primary_input_label" for="">{{trans('account.select_cash_flow_type')}}</label>
                                <ul class="permission_list sms_list">
                                    <li>
                                        <label class="primary_checkbox d-flex mr-12 ">
                                            <input name="cash_in" type="checkbox" id="payment_from_type_1" value="cash_in" class="cash_in filter_type" @if (isset($_GET["cash_in"])) checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{ trans('account.income') }}</p>
                                    </li>
                                    <li>
                                        <label class="primary_checkbox d-flex mr-12 ">
                                            <input name="cash_out" type="checkbox" id="payment_from_type_2" value="cash_out" class="cash_out filter_type" @if (isset($_GET["cash_out"])) checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{ trans('account.expense') }}</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{trans('account.date_from')}}  <small>({{trans('account.date_range')}})</small></label>
                                <div class="primary_datepicker_input">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="position_relative">
                                                @isset($dateFrom)
                                                    <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="fromDate" type="text" name="dateFrom" value="{{ date('m/d/Y', strtotime($dateFrom)) }}" autocomplete="off" required>
                                                    <div class="custom_datepicker_design"></div>
                                                @else
                                                    <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="fromDate" type="text" name="dateFrom" value="" autocomplete="off" required>
                                                    <div class="custom_datepicker_design"></div>
                                                @endisset
                                            </div>
                                        </div>
                                        <button class="date-icon" type="button">
                                            <i class="ti-calendar"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{trans('account.date_to')}}  <small>({{trans('account.date_range')}})</small></label>
                                <div class="primary_datepicker_input">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="position_relative">
                                                @isset($dateTo)
                                                    <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="toDate" type="text" name="dateTo" value="{{ date('m/d/Y', strtotime($dateTo)) }}" autocomplete="off" required>
                                                    <div class="custom_datepicker_design"></div>
                                                @else
                                                    <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="toDate" type="text" name="dateTo" value="" autocomplete="off" required>
                                                    <div class="custom_datepicker_design"></div>
                                                @endisset
                                            </div>
                                        </div>
                                        <button class="date-icon" type="button">
                                            <i class="ti-calendar"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <button class="primary_btn_2 mt-30" type="submit" width="100%"><i class="ti-search"></i>{{ trans('account.search') }}</button>
                        <a href="{{ route('vouchers.cash_flow') }}" class="primary_btn_2 ml-2 mt-30" id="save_button_parent"><i class="fa fa-refresh"></i>{{ trans('account.reset') }}</a>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{ trans('account.cash_flow') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    @if (permissionCheck('vouchers.cash_flow_export_excel'))
                        <button class="primary_btn_2 excel_btn" type="button" width="100%">{{ trans('account.download_excel') }}</button>
                    @endif
                    <button class="primary_btn_2 print_btn" type="button" width="100%">{{ trans('account.print') }}</button>
                </div>
            </div>
            <div class="white_box_50px p-5">
                <div class="row">
                    <div class="col-xl-6 col-md-12">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.income') }}</h3>
                            </div>
                        </div>
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table">
                                <div class="">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ trans('account.cash_flow_account') }}</th>
                                                <th scope="col">{{ trans('account.code') }}</th>
                                                <th scope="col">{{ trans('account.amount') }}</th>
                                            </tr>
                                        </thead>
                                        @if (isset($incomes))
                                            <tbody>
                                                @foreach ($incomes as $key => $income)
                                                    <tr>
                                                        <td>{{ $income->first()->cash_flow_account->name }}</td>
                                                        <td>{{ $income->first()->cash_flow_account->code }}</td>
                                                        <td>{{ single_price($income->sum('amount')) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-12">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.expense') }}</h3>
                            </div>
                        </div>
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table">
                                <div class="">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ trans('account.cash_flow_account') }}</th>
                                                <th scope="col">{{ trans('account.code') }}</th>
                                                <th scope="col">{{ trans('account.amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($expenses))
                                                @foreach ($expenses as $key => $expense)
                                                    <tr>
                                                        <td>{{ $expense->first()->cash_flow_account->name }}</td>
                                                        <td>{{ $expense->first()->cash_flow_account->code }}</td>
                                                        <td>{{ single_price($expense->sum('amount')) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push("scripts")
    <script type="text/javascript" src="{{asset('public/backEnd/js/daterangepicker.min.js')}}"></script>
    <script src="{{ Module::asset('account:cash_flow.js') }}"></script>
@endpush
