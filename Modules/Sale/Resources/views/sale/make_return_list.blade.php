@extends('backEnd.master')
@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('sale.Sales List')}}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <div id="item_list_tbl">
                        @include('sale::sale.paginate.make_return_list')
                    </div>
                    <!-- table-responsive -->
                </div>
            </div>
        </div>
    </div>
    <div id="getDetails">
    </div>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'make_sale_rtn_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','',''],['2','{{ __('sale.Sl') }}','date'],['3','{{ __('sale.Date') }}','type'],['4','{{ __('sale.Invoice') }}','invoice_no'],['5','{{ __('sale.User') }}','user'],['6','{{ __('common.Customer') }}','customer_name'],['7','{{ __('common.Total Amount') }}','total_amount'],['8','{{ __('sale.Paid') }}','paid'],['9','{{ __('sale.Due') }}','due'],['10','{{ __('common.Status') }}','status'],['11','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['make_sale_rtn_list']) }}">

    @include('backEnd.partials.delete_modal')
    @include('backEnd.partials.approve_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script>
        function saleInfo(id) {
            let input = '<input type="hidden" name="id" value="' + id + '" "> ';
            $('#delivery_info').append(input);
        }

        function shippingInfo(id) {
            $.ajax({
                method: 'POST',
                url: '{{route('sale.shipping_info')}}',
                data: {
                    id: id,
                    _token: "{{csrf_token()}}",
                },
                success: function (result) {
                    $('.view_shipping_name').text(result.shipping_name);
                    $('.view_shipping_ref').text(result.shipping_ref);
                    $('.view_date').text(result.date);
                    $('.view_received_by').text(result.received_by);
                    $('.view_received_date').text(result.received_date);
                }
            })
        }

        function getDetails(el){
            $.post('{{ route('get_sale_details') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
                $('#getDetails').html(data);
                $('#sale_info_modal').modal('show');
                $('select').niceSelect();
            });
        }
    </script>
@endpush
