@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings("site_title") .' | '. trans('account.transactions'))
@push('css')
    <style media="screen">
    .QA_section .QA_table th, .QA_section .QA_table td {
        border: 1px solid !important;
    }
    .QA_section .QA_table .table thead th {
        border-bottom: 0 solid transparent !important;
    }
    th {
        text-align: center;
    }
    td {
        text-align: center;
    }
        .dataTables_filter > label {
            top: -5px!important;
        }
        div.dt-buttons {
            top: 2px!important;
        }
        .table_modal thead th {
            border-bottom: 2px solid var(--border_color);
            padding: 5px;
            text-align: center;
            font-weight: 500 !important;
            white-space: nowrap;
            margin-top: 5px;
            border-top: 2px solid var(--border_color) !important;
        }
        .table_modal_upper tbody td {
            padding: 5px 5px 5px 5px !important;
            font-weight: 400 !important;
            max-width: 5px;
        }
        .table_modal tbody td {
            padding: 5px 5px 5px 5px !important;
            font-weight: 400 !important;
            max-width: 5px;
            text-align: center;
        }
        .nowrap {
            white-space: nowrap;
        }

        .QA_section.QA_section_heading_custom .QA_table .table tbody tr td {
            white-space: nowrap !important;
            padding-left: 10px !important;
        }
    </style>
@endpush
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
                        <form class="" action="{{ route('transaction.transactions') }}" method="GET">
                            <div class="row">
                                <div class="col">
                                    <div class="primary_input mb-25">
                                        <div class="double_label d-flex justify-content-between">
                                            <label class="primary_input_label" for="">{{ __('inventory.Branch') }}</label>
                                        </div>
                                        @if (permissionCheck('showroom.get_showroom_for_select'))
                                            <select class="select2 mb-25 showroom_id" name="showroom_id" id="showroom_id" required>
                                                @isset($selected_showroom)
                                                    <option value="{{ $selected_showroom->id }}">{{ $selected_showroom->name }}</option>
                                                @else
                                                    <option value="0">{{ trans('account.Select one') }}</option>
                                                @endisset
                                            </select>
                                        @else
                                            <select class="primary_select mb-25" name="showroom_id" id="showroom_id" required>
                                                @isset($selected_showroom)
                                                    <option value="{{ $selected_showroom->id }}" selected>{{ $selected_showroom->name }}</option>
                                                @else
                                                    <option value="{{ auth()->user()->current_showroom_id }}" selected>{{ auth()->user()->current_showroom->name }}</option>
                                                @endisset
                                            </select>
                                        @endif
                                        <span class="text-danger">{{$errors->first('showroom_id')}}</span>
                                    </div>
                                </div>
                                <div class="col">
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
                                <div class="col">
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
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ trans('account.leadger_account') }} *</label>
                                        <select class="select2 mb-15 account_id" name="leadgerId" id="leadgerId" required>
                                            <option value="0">{{trans('account.Select one')}}</option>
                                            @if (isset($leadgerAccount))
                                                <option value="{{ $leadgerAccount->id }}" selected>{{ $leadgerAccount->name }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="ti-search"></i>{{ trans('account.search') }}</button>
                                </div>

                                <div class="primary_input ml-2">
                                    <a href="{{ route('transaction.transactions') }}" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="fa fa-refresh"></i>{{ trans('account.reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.transactions') }}</h3>
                                <ul class="d-flex">
                                    @if(strpos($_SERVER['REQUEST_URI'], '?') == true)
                                        <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                                           href="{{Illuminate\Support\Facades\Request::fullUrl()}}&print=1"><i
                                                class="ti-printer"></i>{{trans('account.print')}}</a>
                                        {{-- <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                                           href="{{Illuminate\Support\Facades\Request::fullUrl()}}&pdf=1"><i
                                                class="ti-printer"></i>{{trans('account.pdf')}}</a> --}}
                                    @else
                                        <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                                           href="{{Illuminate\Support\Facades\Request::fullUrl()}}?print=1"><i
                                                class="ti-printer"></i>{{trans('account.print')}}</a>
                                        {{-- <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                                           href="{{Illuminate\Support\Facades\Request::fullUrl()}}?pdf=1"><i
                                                class="ti-printer"></i>{{trans('account.pdf')}}</a> --}}
                                    @endif
                                </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <table class="table Crm_table_active_3">
                                <thead>
                                    <tr>
                                        <th scope="col" rowspan="2">{{ trans('account.date') }}</th>
                                        <th scope="col" rowspan="2">{{ trans('account.leadger_account') }}</th>
                                        <th scope="col" rowspan="2">{{trans('account.txn_id')}}</th>
                                        <th scope="col" rowspan="2">{{ trans('account.note') }}</th>
                                        <th scope="col" colspan="2">{{ trans('account.amount') }}</th>
                                    </tr>
                                    <tr>
                                        <th scope="col">{{ trans('account.debit') }}</th>
                                        <th scope="col">{{ trans('account.credit') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="datas">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="get_leadger_list_url" name="get_leadger_list_url" value="{{ route('leadger.get_data_list_for_select') }}">
        <input type="hidden" id="currency_symbol" name="currency_symbol" value="{{ Settings('currency_symbol') }}">
        <input type="hidden" name="search_url" id="search_url" @if(strpos($_SERVER['REQUEST_URI'], '?') == true) value="{{explode('?',$_SERVER['REQUEST_URI'])[1]}}" @else value="0" @endif>
        <input type="hidden" id="details_url" name="details_url" value="{{ route('pro-get_voucher_details', ":id") }}">
        <input type="hidden" value="{{route('showroom.get_showroom_for_select')}}" id="showroom_list_select_option">
        <div id="Voucher_info"></div>
    </section>
@endsection
@push("scripts")
    <script src="{{ Module::asset('account:transaction.js') }}"></script>
    <script src="{{ Module::asset('account:leadger_report.js') }}"></script>
    <script src="{{ Module::asset('core:showroom.js') }}"></script>
@endpush
