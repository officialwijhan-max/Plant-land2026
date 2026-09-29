@extends('backEnd.master')
@section('page-title', app('general_setting')->site_title .' | POS Sales List')
@section('mainContent')
    <div class="row justify-content-between">
        <div class="col-xl-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__('sale.POS Sale')}} </h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <!-- table-responsive -->
                    <div class="">
                        <div id="item_list_tbl">
                            @if (request()->is('sales-section/pos/pos-order-draft-products'))
                                @include('sale::pos_order.paginate.draft_list')
                            @else
                                @include('sale::pos_order.paginate.list')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden"  value="{{ app('general_setting')->currency_symbol }}">
    <div id="getDetails">
    </div>

    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'pos_sale_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','',''],['2','{{ __('sale.Sl') }}','date'],['3','{{ __('sale.Date') }}','type'],['4','{{ __('sale.Invoice') }}','invoice_no'],['5','{{ __('sale.User') }}','user'],['6','{{ __('common.Customer') }}','customer_name'],['7','{{ __('common.Total Amount') }}','total_amount'],['8','{{ __('sale.Paid') }}','paid'],['9','{{ __('sale.Due') }}','due'],['10','{{ __('common.Status') }}','status'],['11','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['pos_sale_list']) }}">

    @include('backEnd.partials.delete_modal')
    @include('backEnd.partials.approve_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script>

    let _token = $('meta[name=_token]').attr('content') ;
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
    $(document).on('click', '.delete_approved_leadger', function(event){
        event.preventDefault();
        let id = $(this).data('id');
        $('#delete_approved_item_id').val(id);
        $('#deleteApprovedDeleteModal').modal('show');
    });
    $(document).on('submit', '#approved_voucher_delete_form', function(event) {
        event.preventDefault();
        $('#pre-loader').removeClass('d-none');
        $('#deleteApprovedDeleteModal').modal('hide');
        let formData = new FormData();
        formData.append('_token', _token);
        formData.append('id', $('#delete_approved_item_id').val());
        formData.append('password', $('#password').val());
        let id = $('#delete_approved_item_id').val();
        $.ajax({
            url: $('#delete_url_2').val(),
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function(response) {
                console.log("s ->" + response);
                toastr.success(response.message);
                location.reload();
                $('#pre-loader').addClass('d-none');
            },
            error: function(error) {
                $('#pre-loader').addClass('d-none');
                toastr.error(error.message)
            }
        });
    });
</script>
@endpush