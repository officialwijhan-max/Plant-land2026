@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings("site_title") .' | '. trans('account.cash_flow_account_reports'))
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.select_criteria') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="white_box_50px box_shadow_white pb-3">
                        <form class="" action="{{ route('cash_flow_report.cash_flow_view') }}" method="GET">
                            <div class="row">
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.date_from')}}  <small>({{trans('account.date_range')}})</small></label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="position_relative">
                                                        @isset($dateFrom)
                                                            <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="fromDate" type="text" name="dateFrom" value="{{ date('m/d/Y', strtotime($dateFrom)) }}" autocomplete="off">
                                                            <div class="custom_datepicker_design"></div>
                                                        @else
                                                            <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="fromDate" type="text" name="dateFrom" value="" autocomplete="off">
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
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.date_to')}}  <small>({{trans('account.date_range')}})</small></label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="position_relative">
                                                        @isset($dateTo)
                                                            <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="toDate" type="text" name="dateTo" value="{{ date('m/d/Y', strtotime($dateTo)) }}" autocomplete="off">
                                                            <div class="custom_datepicker_design"></div>
                                                        @else
                                                            <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="toDate" type="text" name="dateTo" value="" autocomplete="off">
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
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ trans('account.cash_flow_account') }} *</label>
                                        <select class="select2 mb-15 account_id" name="cashflowId" id="cashflowId" required>
                                            <option value="0">{{trans('account.Select one')}}</option>
                                            @if (isset($cashFlowAccount))
                                                <option value="{{ $cashFlowAccount->id }}" selected>{{ $cashFlowAccount->name }}</option>
                                            @endif
                                        </select>
                                        <span class="text-danger">{{$errors->first('cashflowId')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="ti-search"></i>{{ trans('account.search') }}</button>
                                </div>

                                <div class="primary_input ml-2">
                                    <a href="{{ route('cash_flow_report.cash_flow_view') }}" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="fa fa-refresh"></i>{{ trans('account.reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @isset($transactions)
                    <div class="col-12">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.cash_flow_account_reports') }}</h3>
                                <ul class="d-flex">
                                    <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                                           href="{{Illuminate\Support\Facades\Request::fullUrl()}}&print=1"><i
                                                class="ti-printer"></i>{{trans('account.print')}}</a>
                                    </li>
                                    {{-- <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                                           href="{{Illuminate\Support\Facades\Request::fullUrl()}}&pdf=1"><i
                                                class="ti-printer"></i>{{trans('account.pdf')}}</a>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table">
                                @if ($account_type == 1 || $account_type == 3)
                                    @include('proaccount::reports.cash_flow_report.component.debit_transaction_list_table')
                                @else
                                    @include('proaccount::reports.cash_flow_report.component.credit_transaction_list_table')
                                @endif
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
        <input type="hidden" value="{{route('cash_flow_account.get_data_list_for_select')}}" id="cash_flow_list_select_option">
    </section>
    <div id="getDetails">

    </div>
@endsection
@push("scripts")
    <script src="{{ Module::asset('account:cash_flow_report.js') }}"></script>
@endpush
