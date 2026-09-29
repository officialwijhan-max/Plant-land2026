@extends('backEnd.master', ['title' => __('setting.State')])

@section('mainContent')

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="box_header common_table_header xs_mb_0">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('setting.State') }}</h3>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-lg-12 mb-30">
                    <div class="white_box_50px box_shadow_white pt-2 pb-3">

                        {!! Form::open(['route' => 'setup.state.index', 'method' => 'get', 'id' => 'content_form']) !!}

                        <div class="row">
                            <div class="primary_input col-md-6">
                                {{Form::label('country_id', __('setting.Country'), ['class' => 'required'])}}
                                {{Form::select('country_id', $countries, $country_id, ['class' => 'primary_select', 'id' => 'country_id', 'data-placeholder' => __('setting.Select country'),  'data-parsley-errors-container' => '#country_id_error'])}}
                                <span id="country_id_error"></span>
                            </div>

                            <div class="primary_input mt_30 col-md-6">
                                <button type="submit" class="primary-btn fix-gr-bg" id="submit" value="submit" style="width: 100%;"><i class="ti-search"></i>{{ __('setting.Get List') }}</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div> --}}

                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('setup::state.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'state_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{ __('common.ID') }}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('setup.Country') }}','state'],['4','{{ __('common.Action') }}']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self', ['state_list']) }}">

    @includeIf('backEnd.partials.delete_ajax_modal')

@endsection
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
