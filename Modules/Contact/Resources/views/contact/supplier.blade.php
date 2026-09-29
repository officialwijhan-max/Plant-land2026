@extends('backEnd.master')
@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{__("common.Suppliers")}} </h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <!-- table-responsive -->
                    <div id="item_list_tbl">
                        @include('contact::contact.paginates.supplier')
                    </div>
                </div>
            </div>
        </div>
        @include('backEnd.partials.delete_modal')
    </div>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'supplier_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.sl')}}','id'],['2','{{ __('common.Contact ID') }}','contact_id'],['3','{{ __('setting.Active') }}','is_active'],['4','{{ __('common.Customer Name') }}','name'],['5','{{__('common.Email')}}','email'],['6','{{__('common.Phone')}}','mobile'],['7','{{__('common.Pay Term')}}','pay_term_condition'],['8','{{__('common.Tax Number')}}','tax_number'],['9','{{__('common.action')}}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['supplier_list']) }}">
@endsection

@push("scripts")
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
   <script type="text/javascript">
       function update_active_status(el){
           "use strict";
           if(el.checked){
               var status = 1;
           }
           else{
               var status = 0;
           }
           $.post('{{ route('contact.update_active_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
               if(data == 1){
                   toastr.success(trans('js.Updated Successfully'), trans('js.Success'));
               }
               else{
                   toastr.error(trans('js.Something is not right'));
               }
           });
       }
    </script>
@endpush
