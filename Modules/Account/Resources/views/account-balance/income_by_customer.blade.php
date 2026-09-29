
@extends('backEnd.master', ['title' => 'Income by customer'])
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12 mt-5">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('attendance.Select Criteria') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="white_box_50px box_shadow_white">
                        <form class="" action="{{ route('income_by_customer') }}" method="GET">
                            <div class="row">


                                <div class="col-md-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{__('report.Date Range')}}</label>

                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">

                                                            <input placeholder="{{ __('report.Date Range') }}" class="primary_input_field primary-input form-control"  type="text" name="date_range" value="">


                                                    </div>
                                                </div>
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="start-date-icon"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-3 mt-4">
                                    <div class="primary_input mb-15">
                                        <button type="submit" class="primary-btn fix-gr-bg w-100" id="save_button_parent"><i class="ti-search"></i>{{ __('attendance.Search') }}</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>




                    <div class="col-12 mt-5">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                {{-- <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('attendance.Income by customer') }}</h3> --}}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="QA_section QA_section_heading_custom check_box_table">
                            <div class="QA_table ">
                                <div id="item_list_tbl">
                                    @include('account::account-balance.paginates.income_by_customer')
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </section>
    <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @if (strpos($_SERVER['REQUEST_URI'], '?') == true)
        <input type="hidden" name="current_page_url" id="current_page_url" value="{{ url()->full() }}">
    @else
        <input type="hidden" name="current_page_url" id="current_page_url" value="{{ url()->full().'?' }}">
    @endif
    <div id="Voucher_info"></div>
    @include('backEnd.partials.delete_modal')
@endsection

@push("scripts")
<script src="{{ Module::asset('tables:payroll_table.js') }}"></script>
    <script type="text/javascript">
        $(document).on('click', '.voucher_details', function (){
            var id = $(this).data('id');
            voucher_detail(id);
        })

        $(function() {
            "use strict";
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
                }, function(start, end, label) {
                  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
                });
        });



        function voucher_detail(el){
            $.post('{{ route('get_voucher_details') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
                $('#Voucher_info').html(data);
                $('#Voucher_info_modal').modal('show');
                $('select').niceSelect();
            });
        }
    </script>
@endpush
