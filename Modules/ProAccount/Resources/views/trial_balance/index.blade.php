@extends('backEnd.master',['datatable' => TRUE])
@section('page-title', Settings('site_title') .' | '. trans('account.trial_balance'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{ trans('account.trial_balance') }}</h3>
                            <ul class="d-flex">
                                <li><button type="button" class="primary-btn radius_30px mr-10 fix-gr-bg download_excel"><i class="ti-import"></i>{{ trans('account.download_excel') }}</button type="button"></li>
                                <li><button type="button" class="primary-btn radius_30px mr-10 fix-gr-bg download_pdf"><i class="ti-book"></i>{{ trans('account.print') }}</button type="button"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 mb-3">
                    <div class="white_box_50px box_shadow_white pb-3">
                        <form class="" action="{{ route('vouchers.trial_balance') }}" method="GET">
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
                                                        @isset($start_date)
                                                            <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="fromDate" type="text" name="dateFrom" value="{{ date('m/d/Y', strtotime($start_date)) }}" autocomplete="off" required>
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
                                                        @isset($end_date)
                                                            <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="toDate" type="text" name="dateTo" value="{{ date('m/d/Y', strtotime($end_date)) }}" autocomplete="off" required>
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
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="ti-search"></i>{{ trans('account.search') }}</button>
                                </div>

                                <div class="primary_input ml-2">
                                    <a href="{{ route('vouchers.trial_balance') }}" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="fa fa-refresh"></i>{{ trans('account.reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-xl-12 col-md-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="" id="trial_reports_list">
                                @include('proaccount::trial_balance.components.list_tbl', ['leadgers' => $leadgers])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" id="current_url" name="current_url" value="{{ route('vouchers.trial_balance') }}">
    <input type="hidden" value="{{route('showroom.get_showroom_for_select')}}" id="showroom_list_select_option">
@endsection

@push("scripts")
    <script type="text/javascript" src="{{asset('public/backend/js/daterangepicker.min.js')}}"></script>
    <script src="{{ Module::asset('account:trial_balance.js') }}"></script>
    <script src="{{ Module::asset('core:showroom.js') }}"></script>
@endpush
