@extends('backEnd.master')
@section('mainContent')
    <div class="row">
        <div class="col-lg-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('common.Activity Logs') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mb-3">
            <div class="white_box_50px box_shadow_white pb-3">
                <form class="" action="{{ route('activity_log') }}" method="GET">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('common.User') }}</label>
                                <select class="select2 mb-15 single_select primary_singleSelect user_staff" id="user_id" name="user" required>
                                    <option>{{__('common.Select One')}}</option>
                                    @if(isset($user_info) && $user_info)
                                        <option value="{{ $user_info->id }}" selected>{{$user_info->name}}</option>
                                    @endif
                                </select>
                                <span class="text-danger" id="user_error"></span>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{__('report.From Date')}} </label>
                                <div class="primary_datepicker_input">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="position_relative">
                                                <input placeholder="{{__('report.From Date')}}" class="primary_input_field primary-input date form-control" id="from_date" type="text" name="from_date" value="{{isset($from_date) ? date('m/d/Y', strtotime($from_date)) : date('m/d/Y')}}" autocomplete="off">
                                                <div class="custom_datepicker_design"></div>
                                            </div>
                                        </div>
                                        <button class="date-icon" type="button">
                                            <i class="ti-calendar"></i>
                                        </button>
                                    </div>
                                    <span class="text-danger">{{$errors->first('from_date')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{__('report.To Date')}} </label>
                                <div class="primary_datepicker_input">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="position_relative">
                                                <input placeholder="{{__('report.To Date')}}" class="primary_input_field primary-input date form-control" id="to_date" type="text" name="to_date" value="{{isset($to_date) ? date('m/d/Y', strtotime($to_date)) : date('m/d/Y')}}" autocomplete="off">
                                                <div class="custom_datepicker_design"></div>
                                            </div>
                                        </div>
                                        <button class="date-icon" type="button">
                                            <i class="ti-calendar"></i>
                                        </button>
                                    </div>
                                    <span class="text-danger">{{$errors->first('to_date')}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                            <div class="primary_input">
                                <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i
                                        class="ti-search"></i>{{ __('role.Search') }}</button>
                            </div>
                            <div class="primary_input ml-2">
                                <a href="{{route('activity_log')}}" class="primary-btn fix-gr-bg" id="save_button_parent"><i
                                        class="fa fa-refresh"></i>{{ __('report.Reset') }}</a>
                            </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <div id="item_list_tbl">
                        @include('useractivitylog::paginates.list')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="user_select_list_option" id="user_select_list_option" value="{{ route('user.select_all_user') }}">
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'activity_logs')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('common.Description') }}','description'],['3','{{ __('common.Type') }}','type'],['4','{{ __('setting.URL') }}','url'],['5','{{ __('setting.IP') }}','ip'],['6','{{ __('setting.Agent') }}','agent'],['7','{{ __('common.Attempted At') }}','attempted_at'],['8','{{ __('common.User') }}','user']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['activity_logs']) }}">
    <input type="hidden" name="current_page_url" id="current_page_url" value="{{ url()->full() }}">
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:payroll_table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script src="{{ Module::asset('core:staff_select.js') }}"></script>
@endpush
