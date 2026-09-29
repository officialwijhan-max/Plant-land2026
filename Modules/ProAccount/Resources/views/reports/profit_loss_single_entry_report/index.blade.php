@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings("site_title") .' | '. trans('account.profit_loss'))
@push('css')
    <style media="screen">
        .mother_leadger {
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #a134eb !important;
        }
        .cash_detail, .bank_detail {
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #a745e9c4 !important;
        }
    </style>
@endpush
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.profit_loss') }}</h3>
                            <ul class="d-flex">
                                <li>
                                    <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" href="{{Illuminate\Support\Facades\Request::fullUrl()}}&print=1">
                                        <i class="ti-printer"></i>{{trans('account.print')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mb-4">
                    <div class="white_box_50px box_shadow_white pb-3">
                        <form class="" action="{{ route('profit_loss_single_entry_report') }}" method="GET">
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
                            </div>
                            <div class="row others_div">
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
                            <div class="row justify-content-center">
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="ti-search"></i>{{ trans('account.search') }}</button>
                                </div>

                                <div class="primary_input ml-2">
                                    <a href="{{ route('profit_loss_single_entry_report') }}" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="fa fa-refresh"></i>{{ trans('account.reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @php
                    $total_profit_cash = 0;
                    $total_profit_bank = 0;
                @endphp

                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table">
                            <div class="table-responsive">
                                <table class="table Crm_table_active_3">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{trans('account.date')}}</th>
                                            <th scope="col">{{trans('account.account')}}</th>
                                            <th scope="col" class="text-right">{{trans('account.income')}}</th>
                                            <th scope="col" class="text-right">{{trans('account.expense')}}</th>
                                            <th scope="col" class="text-right">{{trans('account.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="mother_leadger nowrap" colspan="5">{{ trans('account.cash_income') }}</td>
                                        </tr>
                                        @foreach ($cash_data as $key => $cash_info)
                                            @php
                                                $total_profit_cash += $cash_info['dr_amount'];
                                            @endphp
                                            <tr>
                                                <td class="mother_leadger nowrap"><a href="javascript:void(0)" class="cash_detail" data-id="data_{{ $key }}">{{ $cash_info['date'] }}</a></td>
                                                <td>{{ $cash_info['leadger_name'] }}</td>
                                                <td class="text-right">{{ single_price($cash_info['dr_amount']) }}</td>
                                                <td class="text-right">{{ single_price($cash_info['cr_amount']) }}</td>
                                                <td class="text-right">{{ single_price($cash_info['dr_amount'] - $cash_info['cr_amount']) }}</td>
                                            </tr>
                                            @foreach ($cash_info['details'] as $detail)
                                            <tr class="data_{{ $key }}_tr d-none">
                                                <td class="nowrap">{{ $detail->date }}</td>
                                                <td>{{ $detail->leadger->name }}</td>
                                                <td class="text-right">{{ ($detail->type == "Dr") ? single_price($detail->amount) : '' }}</td>
                                                <td class="text-right">{{ ($detail->type == "Cr") ? single_price($detail->amount) : '' }}</td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                        <tr>
                                            <td class="nowrap" colspan="4">{{ trans('account.total') }}</td>
                                            <td class="text-right">{{ single_price($total_profit_cash) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="mother_leadger nowrap" colspan="5">{{ trans('account.bank_income') }}</td>
                                        </tr>
                                        @foreach ($bank_data as $m => $bank_info)
                                            @php
                                                $total_profit_bank += $bank_info['dr_amount'];
                                            @endphp
                                            <tr>
                                                <td class="mother_leadger nowrap"><a href="javascript:void(0)" class="bank_detail" data-id="bank_data_{{ $m }}">{{ $bank_info['date'] }}</a></td>
                                                <td>{{ $bank_info['leadger_name'] }}</td>
                                                <td class="text-right">{{ single_price($bank_info['dr_amount']) }}</td>
                                                <td class="text-right">{{ single_price($bank_info['cr_amount']) }}</td>
                                                <td class="text-right">{{ single_price($bank_info['dr_amount'] - $bank_info['cr_amount']) }}</td>
                                            </tr>
                                            @foreach ($bank_info['details'] as $b_detail)
                                            <tr class="bank_data_{{ $m }}_tr d-none">
                                                <td class="nowrap">{{ $b_detail->date }}</td>
                                                <td>{{ $b_detail->leadger->name }}</td>
                                                <td class="text-right">{{ ($b_detail->type == "Dr") ? single_price($b_detail->amount) : '' }}</td>
                                                <td class="text-right">{{ ($b_detail->type == "Cr") ? single_price($b_detail->amount) : '' }}</td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                                        @endforeach
                                        <tr>
                                            <td class="nowrap" colspan="4">{{ trans('account.total') }}</td>
                                            <td class="text-right">{{ single_price($total_profit_bank) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="mother_leadger nowrap" colspan="4">{{ trans('account.grand_total') }}</td>
                                            <td class="text-right">{{ single_price($total_profit_bank + $total_profit_cash) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" value="{{route('cash_flow_account.get_data_list_for_select')}}" id="cash_flow_list_select_option">
        <input type="hidden" value="{{route('showroom.get_showroom_for_select')}}" id="showroom_list_select_option">
    </section>
@endsection
@push("scripts")
    <script src="{{ Module::asset('account:income_statement.js') }}"></script>
    <script src="{{ Module::asset('core:showroom.js') }}"></script>
    <script>
        $(document).on('click','.cash_detail', function(){
            var div = $(this).attr('data-id');
            $('.'+div+'_tr').hasClass('d-none') ? $('.'+div+'_tr').removeClass('d-none') : $('.'+div+'_tr').addClass('d-none');
        });
        $(document).on('click','.bank_detail', function(){
            var div = $(this).attr('data-id');
            $('.'+div+'_tr').hasClass('d-none') ? $('.'+div+'_tr').removeClass('d-none') : $('.'+div+'_tr').addClass('d-none');
        });
    </script>
@endpush
