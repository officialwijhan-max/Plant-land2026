@extends('backEnd.master')
@push('styles')
    <link rel="stylesheet" href="{{asset('public/backEnd/css/custom.css')}}"/>
@endpush
@section('page-title', Settings('site_title') .' | '.__('sale::sale.pending_POS_sale'))
@section('mainContent')
    <form class="" action="{{route("sale_pos.all_approve_instant")}}" method="post">
        @csrf
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="box_header common_table_header">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('sale::sale.pending_POS_sale')}} </h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="QA_section QA_section_heading_custom check_box_table">
                    <div class="QA_table ">
                        <!-- table-responsive -->
                        <div class="">
                            <div id="item_list_tbl">
                                @include('sale::pos_order.paginate.pending_list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="getDetails">
    </div>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'pos_pending_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('common.Date') }}','date'],['3','{{ __('common.Type') }}','type'],['4','{{ __('common.Invoice No') }}','invoice_no'],['5','{{ __('common.Customer Name') }}','customer_name'],['6','{{ __('common.User') }}','user'],['7','{{ __('common.Total Qty') }}','total_qty'],['8','{{ __('common.Tax') }}','tax'],['9','{{ __('common.Total Amount') }}','total_amount'],['10','{{ __('sale::sale.paid') }}','paid'],['11','{{ __('sale::sale.due') }}','due'],['12','{{ __('common.Status') }}','status'],['13','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['pos_pending_list']) }}">
@include('backEnd.partials.approve_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script src="{{ Module::asset('tables:table.js') }}"></script>
    <script>
        function getDetails(el){
            $.post('{{ route('get_sale_details') }}', {_token:'{{ csrf_token() }}', id:el,type:'pos'}, function(data){
                $('#getDetails').html(data);
                $('#sale_info_modal').modal('show');
                $('select').niceSelect();
            });
        }
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            setTimeout(function(){ window.location.reload(); }, 15000);
        }
        $(document).on('change', '.show_due', function () {
            if ($(this).prop('checked') == true)
                $('.previous_due').show();
            else
                $('.previous_due').hide();
        })
        function modal_close() {
            $('#sale_info_modal').remove();
            $('.modal-backdrop').remove();
            window.location.reload();
        }

        function selectAllSales() {
            if ($('.all_product_select').prop('checked') == true) {
                $.each($('.product_select'), function () {
                    $(this).prop('checked', true)
                })
            } else {
                $.each($('.product_select'), function () {
                    $(this).prop('checked', false)
                })
            }
        }
    </script>
@endpush
