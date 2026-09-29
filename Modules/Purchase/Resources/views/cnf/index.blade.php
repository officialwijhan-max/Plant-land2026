@extends('backEnd.master')
@section('mainContent')
    @include("backEnd.partials.alertMessage")
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('purchase.CNF') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('purchase::cnf.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'cnf_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.No')}}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('common.Address') }}','address'],['4','{{ __('common.Email') }}','email'],['5','{{ __('common.Phone') }}','phone'],['6','{{ __('common.Status') }}','status'],['7','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['cnf_list']) }}">
    </section>
    <div id="edit_form"></div>
    <div class="modal fade admin-query" id="CNF_Add">
        <div class="modal-dialog modal_800px modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('common.Add New') }} {{ __('purchase.CNF') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="ti-close "></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('cnf.store') }}" method="POST" id="cnf_addForm">
                        @csrf
                        <div class="row">

                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('common.Name') }} *</label>
                                    <input name="name" class="primary_input_field name" placeholder="{{ __('common.Name') }}" type="text" required>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('common.Email') }}</label>
                                    <input name="email" class="primary_input_field name" placeholder="{{ __('common.Email') }}" type="email">
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('common.Phone') }}</label>
                                    <input name="phone" class="primary_input_field name" placeholder="{{ __('common.Phone') }}" type="text">
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('common.Status') }}</label>
                                    <select class="primary_select mb-25" name="status" id="status">
                                        <option value="1">{{ __('inventory.Active') }}</option>
                                        <option value="2">{{ __('inventory.De-Active') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ __('common.Address') }}</label>
                                    <textarea class="primary_textarea height_112" placeholder="{{ __('common.Address') }}" name="address" spellcheck="false"></textarea>
                                </div>
                            </div>

                            <div class="col-lg-12 text-center">
                                <div class="d-flex justify-content-center pt_20">
                                    <button type="submit" class="primary-btn semi_large2 fix-gr-bg" id="save_button_parent"><i class="ti-check"></i>{{ __('common.Save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script type="text/javascript">
    function edit_warehouse_modal(el){
        $.post('{{ route('cnf.edit.modal') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
            $('#edit_form').html(data);
            $('#Cnf_Edit').modal('show');
            $('select').niceSelect();
        });
    }
</script>
@endpush
