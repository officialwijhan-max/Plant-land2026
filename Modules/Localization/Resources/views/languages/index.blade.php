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
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('common.Language List') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('localization::languages.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'language_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('setting.Code') }}','code'],['4','{{ __('setting.RTL') }}','rtl'],['5','{{ __('setting.Active') }}','active'],['6','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['language_list']) }}">

    </section>
    <div id="edit_form">

    </div>
    <div id="add_laguage_modal">
        <div class="modal fade admin-query" id="language_add">
            <div class="modal-dialog modal_800px modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('common.Add New') }} {{ __('common.Language') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="ti-close "></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('languages.store') }}" method="POST" id="language_addForm">
                            @csrf
                            <div class="row">

                                <div class="col-xl-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('common.Name') }}</label>
                                        <input name="name" class="primary_input_field name" placeholder="{{ __('common.Name') }}" type="text" required>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('setting.Code') }}</label>
                                        <input name="code" class="primary_input_field name" placeholder="{{ __('setting.Code') }}" type="text" required>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('setting.Native Name') }}</label>
                                        <input name="native" class="primary_input_field name" placeholder="{{ __('setting.Native Name') }}" type="text" required>
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
    </div>
@include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script type="text/javascript">
    $( document ).ready(function() {
        $('#add_laguage_modal').hide();
    });
        function open_add_laguage_modal(el){
            $('#add_laguage_modal').modal('show');
            $('#language_add').modal('show');
        }
        function edit_language_modal(el){
            $.post('{{ route('languages.edit') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
                $('#edit_form').html(data);
                $('#Item_Edit').modal('show');
            });
        }
        function update_rtl_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('languages.update_rtl_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    toastr.success("Updated Successfully.","Success");
                }
                else{
                    toastr.error("Something went wrong","error");
                }
            });
        }
        function update_active_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('languages.update_active_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data.success){
                    $('#language_code').html(data.languages);
                    $('select').niceSelect('update');
                    toastr.success(data.success);
                }
                else{
                    toastr.error(data.error);
                }
            });
        }
    </script>
@endpush
