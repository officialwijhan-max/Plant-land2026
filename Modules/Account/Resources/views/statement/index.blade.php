@extends('backEnd.master', ['title' => 'Statement'])
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Statement') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="white_box_50px box_shadow_white pb-3">
                        <form class="" action="{{ route('statement.index') }}" method="GET">

                            <div class="row">
                                <div class="col">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{__('report.Date Range')}}</label>

                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input placeholder="Date" class="primary_input_field primary-input form-control" type="text" name="date_range" value="">
                                                    </div>
                                                </div>
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col" class="account_type1" id="account_type1">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('common.Type') }}</label>
                                        <select class="primary_select mb-25 account_type" id="account_type" name="account_type" required>
                                            <option value="1" @selected(request('account_type') == 1)>{{__('account.Asset')}}</option>
                                            <option value="2" @selected(request('account_type') == 2)>{{__('account.Liability')}}</option>
                                            <option value="3" @selected(request('account_type') == 3)>{{__('account.Expense')}}</option>
                                            <option value="4" @selected(request('account_type') == 4)>{{__('account.Income')}}</option>
                                            <option value="5" @selected(request('account_type') == 5)>{{__('account.Equity')}}</option>
                                        </select>
                                        <span class="text-danger" id="name_error"></span>
                                    </div>
                                </div>


                                <div class="col">
                                    <div class="primary_input mb-15" id="select_account">
                                        <label class="primary_input_label" for="">{{ __('report.Account') }}</label>
                                        <select class="select2 mb-15 single_select primary_singleSelect payment_to" name="account_id" required>
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
                                    <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i
                                            class="ti-search"></i>{{ __('attendance.Search') }}</button>
                                </div>

                                <div class="primary_input ml-2">
                                    <a href="{{route('statement.index')}}" class="primary-btn fix-gr-bg"
                                       id="save_button_parent"><i class="fa fa-refresh"></i>{{ __('report.Reset') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @isset($transactions)
                    <div class="col-12">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Statement') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table">
                                <!-- table-responsive -->
                                @if ($accont_type == 2 || $accont_type == 4)
                                    @include('account::statement.credit_transaction_list_table')
                                @else
                                    @include('account::statement.debit_transaction_list_table')
                                @endif
                            </div>
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </section>

    <input type="hidden" name="expense_account_select_list_option" id="expense_account_select_list_option" data-type="{{ request('account_type') ? request('account_type') : 1 }}" value="{{ route('account.select_based_on_type') }}">

    <div id="Voucher_info_statement"></div>
@endsection
@push('scripts')
<script src="{{ Module::asset('core:expense_accounts.js') }}"></script>
    <script>
        $(function () {
            $('input[name="date_range"]').daterangepicker({
                ranges: {
            {!! json_encode(__('calender.Today')) !!}: [moment(), moment()],
                    {!! json_encode(__('calender.Yesterday')) !!}: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    {!! json_encode(__('calender.Last 7 Days')) !!}: [moment().subtract(6, 'days'), moment()],
                    {!! json_encode(__('calender.Last 30 Days')) !!}: [moment().subtract(29, 'days'), moment()],
                    {!! json_encode(__('calender.This Month')) !!}: [moment().startOf('month'), moment().endOf('month')],
                    {!! json_encode(__('calender.Last Month')) !!}: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                "locale": {
                    "separator": {!! json_encode(__('calender.separator')) !!},
                    "applyLabel": {!! json_encode(__('calender.applyLabel')) !!},
                    "cancelLabel": {!! json_encode(__('calender.cancelLabel')) !!},
                    "fromLabel": {!! json_encode(__('calender.fromLabel')) !!},
                    "toLabel": {!! json_encode(__('calender.toLabel')) !!},
                    "customRangeLabel": {!! json_encode(__('calender.customRangeLabel')) !!},
                    "weekLabel": {!! json_encode(__('calender.weekLabel')) !!},
                    "daysOfWeek": {!! json_encode(__('calender.daysMin')) !!},
                    "monthNames": {!! json_encode(__('calender.months')) !!}
                },
                "startDate": moment().subtract(7, 'days'),
                "endDate": moment()
            }, function (start, end, label) {
                console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            });
        });


        function voucher_detail1(el) {
            $.post('{{ route('get_voucher_details_form_statetment') }}', {
                _token: '{{ csrf_token() }}',
                id: el
            }, function (data) {

                $('#Voucher_info_statement').html(data);
                $('#Voucher_statement_info_modal').modal('show');
                $('select').niceSelect();
            });
        }


        $(document).on('change', '.account_type', function () {
            var type = $(this).val();
            $('.payment_to').append($('<option>', {value: 0,text: 'Select One'}));
            $('.payment_to').val(0);
            $('.payment_to').trigger("change");
            $('#expense_account_select_list_option').attr('data-type',type);
        })

    </script>
@endpush
