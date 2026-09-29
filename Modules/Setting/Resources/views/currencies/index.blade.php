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
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('setting.Currency List') }}</h3>
                            @if(permissionCheck('currencies.store'))
                            <ul class="d-flex">
                                <li></li>
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('setting::currencies.paginates.list')
                            </div>
                            <!-- table-responsive -->
                            {{-- <div class="">
                                <table class="table Crm_table_active3">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{ __('common.ID') }}</th>
                                        <th scope="col">{{ __('common.Name') }}</th>
                                        <th scope="col">{{ __('setting.Code') }}</th>
                                        <th scope="col">{{ __('setting.Symbol') }}</th>
                                        <th scope="col">{{ __('common.Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($currencies as $key=>$currency)
                                        <tr>
                                            <th>{{ $key+1 }}</th>
                                            <td>{{ $currency->name }}</td>
                                            <td>{{ $currency->code }}</td>
                                            <td>{{ $currency->symbol }}</td>
                                            <td>
                                                <!-- shortby  -->
                                                <div class="dropdown CRM_dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenu2" data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        {{ __('common.Select') }}
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
                                                        @if(permissionCheck('currencies.edit'))
                                                        <a href="javascript:void(0)" class="dropdown-item edit_brand" onclick="edit_currency_modal({{ $currency->id }})">{{__('common.Edit')}}</a>
                                                        @endif
                                                        @if(permissionCheck('currencies.destroy'))
                                                        @if ($currency->id > 120)
                                                            <a onclick="confirm_modal('{{route('currencies.destroy', $currency->id)}}');" class="dropdown-item edit_brand">{{__('common.Delete')}}</a>
                                                        @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- shortby  -->
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="edit_form">

    </div>
    <div id="add_currency_modal">
        <div class="modal fade admin-query" id="currency_add">
            <div class="modal-dialog modal_800px modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('common.Add New') }} {{ __('common.Currency') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="ti-close "></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('currencies.store') }}" method="POST" id="currency_addForm">
                            @csrf
                            <div class="row">

                                <div class="col-xl-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('common.Name') }}</label>
                                        <input name="name" class="primary_input_field name" placeholder="Dollar" type="text" required>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('setting.Code') }}</label>
                                        <input name="code" class="primary_input_field name" placeholder="USD" type="text" required>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('setting.Symbol') }}</label>
                                        <input name="symbol" class="primary_input_field name" placeholder="$" type="text" required>
                                    </div>
                                </div>

                                <div class="col-lg-12 text-center">
                                    <div class="d-flex justify-content-center pt_20">
                                        <button type="submit" class="primary-btn semi_large2  fix-gr-bg"
                                                id="save_button_parent"><i
                                                class="ti-check"></i>{{ __('common.Save') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'currency_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('setting.Code') }}','code'],['4','{{ __('setting.Symbol') }}','symbol'],['5','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['currency_list']) }}">

@include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#add_currency_modal').hide();
        });
        function open_add_currency_modal(el){
            $('#add_currency_modal').modal('show');
            $('#currency_add').modal('show');
        }
        function edit_currency_modal(el){
            $.post('{{ route('currencies.edit') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
                $('#edit_form').html(data);
                $('#Item_Edit').modal('show');
            });
        }
    </script>
@endpush
