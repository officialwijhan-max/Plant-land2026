@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('common.Payment Requests') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div id="item_list_tbl">
                                @include('account::payment_request.accountant_list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'expense_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('inventory.Branch Name') }}','name'],['3','{{ __('inventory.TXN id') }}','reference'],['4','{{ __('common.Amount') }}','amount'],['5','{{ __('account.Approved') }}','status'],['6','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['expense_list']) }}">
    
    <div id="Voucher_info"></div>
@include('backEnd.partials.delete_modal')
@endsection

@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script type="text/javascript">
        $(document).on('click', '.get_details_voucher', function(){
            var id = $(this).attr('data-id');
            var url = $(this).attr('data-route');
            $.get(url, function(data){
                $('#Voucher_info').html(data);
                $('#Voucher_info_modal').modal('show');
            });
        });
    </script>
@endpush
