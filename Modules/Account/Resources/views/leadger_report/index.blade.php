@extends('backEnd.master', ['title' => 'Transactions Report'])
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Transactions') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="white_box_50px box_shadow_white pb-3">
                        <form class="" action="{{ route('transaction.index') }}" method="GET">
                            <div class="row">
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{__('report.Date From')}} <small>({{__('report.Date Range')}})</small></label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        @isset($dateFrom)
                                                            <input placeholder="{{ __('common.Date') }}" class="primary_input_field primary-input date form-control" id="fromDate" type="text" name="dateFrom" value="{{ date('m/d/Y', strtotime($dateFrom)) }}" autocomplete="off">
                                                        @else
                                                            <input placeholder="{{ __('common.Date') }}" class="primary_input_field primary-input date form-control" id="fromDate" type="text" name="dateFrom" value="" autocomplete="off">
                                                        @endisset
                                                    </div>
                                                </div>
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{__('report.Date To')}} <small>({{__('report.Date Range')}})</small></label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        @isset($dateTo)
                                                            <input placeholder="{{ __('common.Date') }}" class="primary_input_field primary-input date form-control" id="toDate" type="text" name="dateTo" value="{{ date('m/d/Y', strtotime($dateTo)) }}" autocomplete="off">
                                                        @else
                                                            <input placeholder="{{ __('common.Date') }}" class="primary_input_field primary-input date form-control" id="toDate" type="text" name="dateTo" value="" autocomplete="off">
                                                        @endisset
                                                    </div>
                                                </div>
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ __('report.Account') }}</label>
                                        <select class="select2 mb-15 single_select primary_singleSelect account_id" name="account_id" required>
                                            @if ($filter_account)
                                                <option value="{{ $filter_account->id }}">{{ $filter_account->name }}</option>
                                            @else
                                                <option>{{__('common.Select One')}}</option>
                                            @endif
                                        </select>
                                        <span class="text-danger">{{$errors->first('account_id')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="primary_input">
                                    <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="ti-search"></i>{{ __('attendance.Search') }}</button>
                                </div>

                                <div class="primary_input ml-2">
                                    <a href="{{route('transaction.index')}}" class="primary-btn fix-gr-bg" id="save_button_parent"><i class="fa fa-refresh"></i>{{ __('report.Reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @isset($transactions)
                    <div class="col-12">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Transactions') }}</h3>
                                <ul class="d-flex">
                                    @if(strpos($_SERVER['REQUEST_URI'], '?') == true)
                                        <li>
                                            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=print" class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" title="Print">
                                                <i class="ti-printer"></i>{{ __('common.Print') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}&import_as=csv" class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" title="Export">
                                                <i class="ti-export"></i>{{ __('common.CSV File') }}
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=print" class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" title="Print">
                                                <i class="ti-printer"></i>{{ __('common.Print') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{Illuminate\Support\Facades\Request::fullUrl()}}?import_as=csv" class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank" title="Export">
                                                <i class="ti-export"></i>{{ __('common.CSV File') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table">
                                <!-- table-responsive -->
                                @if ($accont_type == 1 || $accont_type == 3)
                                    @include('account::leadger_report.debit_transaction_list_table')
                                @else
                                    @include('account::leadger_report.credit_transaction_list_table')
                                @endif
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </section>
    <input type="hidden" name="account_select_list_option" id="account_select_list_option" data-type="4" value="{{ route('account.select_all_list') }}">
    <div id="getDetails">

    </div>
@endsection
@push('scripts')
<script src="{{ Module::asset('core:all_accounts.js') }}"></script>
    <script>
        function voucher_detail(el){
            $.post('{{ route('get_voucher_details') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
                $('#getDetails').html(data);
                $('#Voucher_info_modal').modal('show');
                $('select').niceSelect();
            });
        }
        function getDetails(el){
            $.post('{{ route('get_purchase_details') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
                $('#getDetails').html(data);
                $('#purchase_info_modal').modal('show');
                $('select').niceSelect();
            });
        }
        function getsaleDetails(el){
            $.post('{{ route('get_sale_details') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
                $('#getDetails').html(data);
                $('#sale_info_modal').modal('show');
                $('select').niceSelect();
            });
        }
    </script>
@endpush
