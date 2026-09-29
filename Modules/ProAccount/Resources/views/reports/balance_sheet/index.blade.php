@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings("site_title") .' | '. trans('account.balance_sheet_report'))
@push('css')
    <style media="screen">
        .mother_leadger {
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #a134eb !important;
        }
        .red_input {
            font-size: 15px !important;
            color: #dc2f2f !important;
        }
    </style>
@endpush
@section('mainContent')
    @php
        $difference = [];
    @endphp
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.balance_sheet_report') }}</h3>
                            <ul class="d-flex">
                                @if (Request::get('start_year')!=0 || Request::get('report_type')=="others")
                                    <li>
                                        <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" href="{{Illuminate\Support\Facades\Request::fullUrl()}}&print=1">
                                            <i class="ti-printer"></i>{{trans('account.print')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" href="{{Illuminate\Support\Facades\Request::fullUrl()}}&excel=1">
                                            <i class="ti-download"></i>{{trans('account.download_excel')}}
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" href="{{Illuminate\Support\Facades\Request::fullUrl()}}?excel=1">
                                            <i class="ti-download"></i>{{trans('account.download_excel')}}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mb-4">
                    <div class="white_box_50px box_shadow_white pb-3">
                        <form class="" action="{{ route('balance_sheet_report') }}" method="GET">
                            <div class="row">
                                <div class="col-4">
                                    <label class="primary_input_label" for="">{{trans('account.statement_based_on')}}</label>
                                    <ul class="permission_list sms_list">
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="report_type" type="radio" class="report_type" id="report_type_1" value="fiscal_year" @if (request('report_type') == null || request('report_type') == "fiscal_year") checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ trans('account.financial_years') }}</p>
                                        </li>
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="report_type" type="radio" class="report_type" id="report_type_2" value="others" @if (request('report_type') == "others") checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ trans('account.date_range') }}</p>
                                        </li>
                                    </ul>
                                </div>

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
                            </div>
                            <div class="row others_div {{ (request('report_type') == null || request('report_type') == "fiscal_year") ? 'd-none' : '' }}">
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
                            </div>
                            <div class="row fiscal_year_div {{ (request('report_type') == "others") ? 'd-none' : '' }}"">
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ trans('account.from') }} *</label>
                                        <select class="primary_select mb-15 start_year" name="start_year" id="start_year" required>
                                            <option value="0">{{trans('account.Select one')}}</option>
                                            @foreach ($all_financial_years as $f_year)
                                                <option value="{{ $f_year->id }}" @if (Request::get('start_year') == $f_year->id) selected @endif>{{ date(Settings("date_format_id"), strtotime($f_year->start_date)) }} - {{ ($f_year->end_date != null) ? date(Settings("date_format_id"), strtotime($f_year->end_date)) : 'Continue' }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{$errors->first('start_year')}}</span>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ trans('account.to') }} *</label>
                                        <select class="primary_select mb-15 end_year" name="end_year" id="end_year" required>
                                            <option value="0">{{trans('account.Select one')}}</option>
                                            @foreach ($all_financial_years as $f_year)
                                                <option value="{{ $f_year->id }}" @if (Request::get('end_year') == $f_year->id) selected @endif>{{ date(Settings("date_format_id"), strtotime($f_year->start_date)) }} - {{ ($f_year->end_date != null) ? date(Settings("date_format_id"), strtotime($f_year->end_date)) : 'Continue' }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{$errors->first('end_year')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="ti-search"></i>{{ trans('account.search') }}</button>
                                </div>

                                <div class="primary_input ml-2">
                                    <a href="{{ route('balance_sheet_report') }}" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="fa fa-refresh"></i>{{ trans('account.reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if (request('report_type') == null || request('report_type') == "fiscal_year")
                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table">
                                <div class="table-responsive">
                                    <table class="table Crm_table_active_3">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{trans('account.account')}}</th>
                                                <th scope="col">{{trans('account.note')}}</th>
                                                @foreach ($financial_years as $key => $financial_year)
                                                    <th scope="col" class="text-right">{{ date(Settings("date_format_id"), strtotime($financial_year->start_date)).' - ' }}{{ ($financial_year->end_date != null) ? date(Settings("date_format_id"), strtotime($financial_year->end_date)) : 'Continue' }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="mother_leadger red_input" colspan="{{ count($financial_years) + 2 }}">
                                                    {{ trans('account.assets') }}
                                                </td>
                                            </tr>
                                            @foreach ($assets as $key => $asset)
                                                @if ($asset->parent_id != 0)
                                                    <tr>
                                                        <td class={{ ($asset->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                                                            {{ $asset->name}}
                                                        </td>
                                                        <td class="text-center"></td>
                                                        @foreach ($financial_years as $k => $financial_year)
                                                            <td class="text-center"></td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                                @foreach ($asset->childrenCategories as $child_account)
                                                    @include('proaccount::reports.balance_sheet.component.child_list', ['child_account' => $child_account])
                                                @endforeach
                                            @endforeach
                                            <tr>
                                                <td class="mother_leadger" colspan="2">
                                                    {{ trans('account.total') .' '. trans('account.assets')}}
                                                </td>
                                                @foreach ($financial_years as $j => $financial_year)
                                                    @php
                                                        $running_total_asset = $financial_year->showTotalBalance($assets_ids, $showroom_id);
                                                        $running_total_liability = $financial_year->showTotalBalance($liabilities_ids, $showroom_id);
                                                        $difference[$financial_year->id] = $running_total_asset - $running_total_liability;

                                                    @endphp
                                                    <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($assets_ids, $showroom_id)) }}</td>
                                                @endforeach
                                            </tr>

                                            <tr>
                                                <td class="mother_leadger red_input" colspan="{{ count($financial_years) + 2 }}">
                                                    {{ trans('account.liabilities_and_equity') }}
                                                </td>
                                            </tr>

                                            @foreach ($liabilities as $key => $liability)
                                                @if ($liability->parent_id != 0)
                                                    <tr>
                                                        <td class={{ ($liability->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                                                            {{ $liability->name}}
                                                        </td>
                                                        <td class="text-center"></td>
                                                        @foreach ($financial_years as $k => $financial_year)
                                                            <td class="text-right"></td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                                @foreach ($liability->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $child_account)
                                                    @include('proaccount::reports.balance_sheet.component.child_list', ['child_account' => $child_account])
                                                @endforeach
                                            @endforeach
                                            <tr>
                                                <td class="mother_leadger" colspan="2">
                                                    {{ trans('account.liabilities_and_equity') }}
                                                </td>
                                                @foreach ($financial_years as $j => $financial_year)
                                                    <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($liabilities_ids, $showroom_id)) }}</td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td class="mother_leadger" colspan="2">
                                                    {{ trans('account.approximate_profit_loss') .'('.trans('account.current_period').')'}}
                                                </td>
                                                @foreach ($financial_years as $j => $financial_year)
                                                    <td class="mother_leadger text-right">{{ single_price($difference[$financial_year->id]) }}</td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td class="mother_leadger" colspan="2">
                                                    {{ trans('account.total') .' '. trans('account.liabilities_and_equity') }}
                                                </td>
                                                @foreach ($financial_years as $j => $financial_year)
                                                    <td class="mother_leadger text-right">{{ single_price($financial_year->showTotalBalance($liabilities_ids, $showroom_id) + $difference[$financial_year->id]) }}</td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        $total_direct_income = 0;
                        $total_direct_expense = 0;
                    @endphp
                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table">
                                <div class="table-responsive">
                                    <table class="table Crm_table_active_3">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{trans('account.account')}}</th>
                                                <th scope="col">{{trans('account.note')}}</th>
                                                <th scope="col" class="text-right">{{date('Y-m-d', strtotime($dateFrom)) }} - {{ ($dateTo != null) ? date('Y-m-d', strtotime($dateTo)) : 'Continue' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="mother_leadger red_input" colspan="3">
                                                    {{ trans('account.assets') }}
                                                </td>
                                            </tr>
                                            @foreach ($assets as $key => $asset)
                                                @if ($asset->parent_id != 0)
                                                    <tr>
                                                        <td class={{ ($asset->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                                                            {{ $asset->name}}
                                                        </td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center"></td>
                                                    </tr>
                                                @endif
                                                @php
                                                    foreach ($asset->childrenCategories as $key => $leadger_a) {
                                                        $total_direct_income += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                        foreach ($leadger_a->childrenCategories as $key => $leadger_b) {
                                                            $total_direct_income += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                            foreach ($leadger_b->childrenCategories as $key => $leadger_c) {
                                                                $total_direct_income += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                                foreach ($leadger_c->childrenCategories as $key => $leadger_d) {
                                                                    $total_direct_income += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                                    foreach ($leadger_d->childrenCategories as $key => $leadger_e) {
                                                                        $total_direct_income += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                                        foreach ($leadger_e->childrenCategories as $key => $leadger_f) {
                                                                            $total_direct_income += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @foreach ($asset->childrenCategories as $child_account)
                                                    @include('proaccount::reports.balance_sheet.component.child_list_others', ['child_account' => $child_account])
                                                @endforeach
                                            @endforeach
                                            <tr>
                                                <td class="mother_leadger" colspan="2">
                                                    {{ trans('account.total') .' '. trans('account.assets')}}
                                                </td>


                                                <td class="mother_leadger text-right">{{ single_price($total_direct_income) }}</td>
                                            </tr>

                                            <tr>
                                                <td class="mother_leadger red_input" colspan="{{ count($financial_years) + 2 }}">
                                                    {{ trans('account.liabilities_and_equity') }}
                                                </td>
                                            </tr>
                                            @foreach ($liabilities as $key => $liability)
                                                @if ($liability->parent_id != 0)
                                                    <tr>
                                                        <td class={{ ($liability->is_cost_center == 1) ? "mother_leadger nowrap" : ""}}>
                                                            {{ $liability->name}}
                                                        </td>
                                                        <td class="text-center"></td>
                                                        <td class="text-right"></td>
                                                    </tr>
                                                @endif
                                                @php
                                                    foreach ($liability->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_a) {
                                                        $total_direct_expense += $leadger_a->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                        foreach ($leadger_a->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_b) {
                                                            $total_direct_expense += $leadger_b->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                            foreach ($leadger_b->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_c) {
                                                                $total_direct_expense += $leadger_c->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                                foreach ($leadger_c->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_d) {
                                                                    $total_direct_expense += $leadger_d->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                                    foreach ($leadger_d->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_e) {
                                                                        $total_direct_expense += $leadger_e->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                                        foreach ($leadger_e->childrenCategories->whereNotIn('id',[Settings('retail_earning_leadger')]) as $key => $leadger_f) {
                                                                            $total_direct_expense += $leadger_f->BalanceAmountBetweenDate($dateFrom, $dateTo, $showroom_id);
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                @foreach ($liability->childrenCategories->whereNotIn('id', Settings('retail_earning_leadger')) as $child_account)
                                                    @include('proaccount::reports.balance_sheet.component.child_list_others', ['child_account' => $child_account])
                                                @endforeach
                                            @endforeach
                                            <tr>
                                                <td class="mother_leadger" colspan="2">
                                                    {{ trans('account.liabilities_and_equity') }}
                                                </td>
                                                <td class="mother_leadger text-right">{{ single_price($total_direct_expense) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="mother_leadger" colspan="2">
                                                    {{ trans('account.approximate_profit_loss') .'('.trans('account.current_period').')'}}
                                                </td>
                                                <td class="mother_leadger text-right" colspan="{{ count($financial_years) }}">{{ single_price($total_direct_income - $total_direct_expense) }}</td>
                                            </tr>

                                            <tr>
                                                <td class="mother_leadger" colspan="2">
                                                    {{ trans('account.total') .' '. trans('account.liabilities_and_equity') }}
                                                </td>
                                                <td class="mother_leadger text-right">{{ single_price($total_direct_expense + ($total_direct_income - $total_direct_expense)) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <input type="hidden" value="{{route('cash_flow_account.get_data_list_for_select')}}" id="cash_flow_list_select_option">
        <input type="hidden" value="{{route('showroom.get_showroom_for_select')}}" id="showroom_list_select_option">
    </section>
@endsection
@push("scripts")
    <script src="{{ Module::asset('account:income_statement.js') }}"></script>
    <script src="{{ Module::asset('core:showroom.js') }}"></script>
@endpush
