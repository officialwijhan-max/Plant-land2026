@extends('backEnd.master', ['title' => __('setting.City')])

@section('mainContent')

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('setup.City') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('setup::city.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'city_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{ __('common.ID') }}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('setup.State') }}','state'],['4','{{ __('common.Action') }}']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self', ['city_list']) }}">


    @includeIf('backEnd.partials.delete_ajax_modal')

@stop
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script>
    $(document).ready(function () {
        _formValidation('item_delete_form', true, 'deleteBizAjaxItemModal')
        _componentAjaxChildLoad('#content_form', '#country_id', '#state_id', 'state')
    });
</script>
@endpush
