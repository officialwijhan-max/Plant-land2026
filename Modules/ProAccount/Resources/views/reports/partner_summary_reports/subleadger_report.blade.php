@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings("site_title") .' | '. trans('account.partner_summary_reports'))
@push('css')
    <style>
        .head_color {
            font-weight: 500 !important;
            color: #8633fa !important;
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
                        <form class="" action="{{ route('leadger_report.partner_summary_reports') }}" method="GET">
                            <div class="row">
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ trans('account.partner_account') }} *</label>
                                        <select class="select2 mb-15 subleager" name="subleagerId" id="subleagerId" required>
                                            <option value="0">{{trans('account.Select one')}}</option>
                                            @if (isset($leadgerAccount))
                                                <option value="{{ $leadgerAccount->id }}" selected>{{ $leadgerAccount->name }}</option>
                                            @endif
                                        </select>
                                        <span class="text-danger">{{$errors->first('subleagerId')}}</span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ trans('account.type') }}</label>
                                        <select class="primary_select mb-15 type" name="type" id="type" required>
                                            <option value="all">{{trans('account.Select one')}}</option>
                                            <option value="misc" @if (Request::get('type') == "misc") selected @endif>{{trans('account.miscellaneous')}}</option>
                                            <option value="cash" @if (Request::get('type') == "cash") selected @endif>{{trans('account.cash')}}</option>
                                            <option value="bank" @if (Request::get('type') == "bank") selected @endif>{{trans('account.bank')}}</option>
                                        </select>
                                        <span class="text-danger">{{$errors->first('type')}}</span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{trans('account.date_from')}} <small>({{trans('account.date_range')}})</small></label>
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
                                        <label class="primary_input_label" for="">{{trans('account.date_to')}} <small>({{trans('account.date_range')}})</small></label>
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
                            </div>
                            <div class="row justify-content-center">
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="ti-search"></i>{{ trans('account.search') }}</button>
                                </div>

                                <div class="primary_input ml-2">
                                    <a href="{{ route('leadger_report.partner_summary_reports') }}" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="fa fa-refresh"></i>{{ trans('account.reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @if(isset($transactions) && count($transactions) > 0)
                    <div class="col-12">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                @php
                                    if ($leadgerAccount->morphable_type == "Modules\Hr\Entities\Employee") {
                                        $accountName = "EMPLOYEE ACCOUNT";
                                    }elseif ($leadgerAccount->morphable_type == "Modules\Customer\Entities\Customer") {
                                        $accountName = "CUSTOMER ACCOUNT";
                                    }elseif ($leadgerAccount->morphable_type == "Modules\Purchase\Entities\Supplier") {
                                        $accountName = "SUPPLIER ACCOUNT";
                                    }else {
                                        $accountName = "PARTNER ACCOUNT";
                                    }
                                @endphp
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ $accountName }} : ({{ $leadgerAccount->code }}) {{$leadgerAccount->name}}</h3>
                                <ul class="d-flex">
                                    <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                                           href="{{Illuminate\Support\Facades\Request::fullUrl()}}&print=1"><i
                                                class="ti-printer"></i>{{trans('account.print')}}</a>
                                    </li>
                                    <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                                           href="{{Illuminate\Support\Facades\Request::fullUrl()}}&excel=1"><i
                                                class="ti-import"></i>{{trans('account.excel')}}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table">
                                <table class="table Crm_table_active_3">
                                    <thead>
                                        <tr>
                                            <th>{{ trans("account.account") }}</th>
                                            <th>{{ trans("account.fiscal_year") }}</th>
                                            <th>{{ trans("account.date") }}</th>
                                            <th>{{ trans("account.type") }}</th>
                                            <th width="30%">{{ trans("account.label") }}</th>
                                            <th>{{ trans("account.debit") }}</th>
                                            <th>{{ trans("account.credit") }}</th>
                                            <th>{{ trans("account.balance") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $group_account = [];
                                            $total_debit_amount = 0;
                                            $total_credit_amount = 0;
                                        @endphp
                                        @foreach ($transactions as $key => $transaction)
                                            @if (!in_array($transaction->leadger_id, $group_account))
                                                @php
                                                    array_push($group_account, $transaction->leadger_id);
                                                    $debit_total = 0;
                                                    $credit_total = 0;
                                                @endphp
                                                <tr>
                                                    <td class="head_color" colspan="8">{{ $transaction->leadger->code }} - {{ $transaction->leadger->name }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td></td>
                                                <td>{{  date(Settings("date_format_id"), strtotime($transaction->fiscal_year->start_date)) }}</td>
                                                <td>{{  date(Settings("date_format_id"), strtotime($transaction->date)) }}</td>
                                                <td>
                                                    @if ($transaction->voucher->type == "cash" || $transaction->voucher->type == "rec_cash" || $transaction->voucher->type == "pay_cash")
                                                        {{ trans('account.cash') }}
                                                    @elseif ($transaction->voucher->type == "bank" || $transaction->voucher->type == "rec_bank" || $transaction->voucher->type == "pay_bank")
                                                        {{ trans('account.bank') }}
                                                    @else
                                                        {{ trans('account.miscellaneous') }}
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->voucher->narration }}</td>
                                                <td class="text-right">
                                                    @if ($transaction->type == "Dr")
                                                        @php
                                                            $debit_total += $transaction->amount;
                                                            $total_debit_amount += $transaction->amount;
                                                        @endphp
                                                        {{ single_price($transaction->amount) }}
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($transaction->type == "Cr")
                                                        @php
                                                            $credit_total += $transaction->amount;
                                                            $total_credit_amount += $transaction->amount;
                                                        @endphp
                                                        {{ single_price($transaction->amount) }}
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($transaction->leadger->type == 1 || $transaction->leadger->type == 3)
                                                        {{ single_price($debit_total - $credit_total) }}
                                                    @else
                                                        {{ single_price($credit_total - $debit_total) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="5" class="head_color text-right nowrap">{{ trans("account.cumulated_balance") }}</td>
                                            <td class="head_color text-right nowrap">
                                                {{ single_price($total_debit_amount) }}
                                            </td>
                                            <td class="head_color text-right nowrap">
                                                {{ single_price($total_credit_amount) }}
                                            </td>
                                            <td class="head_color text-right nowrap">
                                                @if ($transaction->leadger->type == 1 || $transaction->leadger->type == 3)
                                                    {{ single_price($total_debit_amount - $total_credit_amount) }}
                                                @else
                                                    {{ single_price($total_credit_amount - $total_debit_amount) }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif(isset($transactions) && count($transactions) == 0)
                    <div class="col-lg-12 mt-10">
                        <div class="white_box_50px box_shadow_white">
                            <div class="alert alert-info">
                                {{ trans("account.no_data_to_show") }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <input type="hidden" id="get_subleadger_list_url" name="get_subleadger_list_url" value="{{ route('sub_leadger.get_data_list_for_select') }}">

        <div id="Voucher_info"></div>
        <input type="hidden" id="currency_sym" name="currency_sym" value="{{ Settings('currency_symbol') }}">
        <input type="hidden" id="details_url" name="details_url" value="{{ route('pro-get_voucher_details', ":id") }}">
    </section>
@endsection
@push("scripts")
    <script src="{{ Module::asset('account:subleadger_summary_report.js') }}"></script>
@endpush
