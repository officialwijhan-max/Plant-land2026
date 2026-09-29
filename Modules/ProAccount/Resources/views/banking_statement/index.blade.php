@extends('backEnd.master')
@section('page-title', Settings("site_title") .' | '. trans('account.banking'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Banking') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('proaccount::banking_statement.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'banking_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Sl')}}','id'],['2','{{ __('account.Date') }}','date'],['3','{{ __('account.Account') }}','account'],['4','{{ __('account.Balance') }}','balance'],['5','{{ __('account.Reconciled') }}','reconciled'],['6','{{ __('common.Uploaded By') }}','uploaded_by'],['6','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['banking_list']) }}">
    </section>
    @include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
    <script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script src="{{ Module::asset('tables:table.js') }}"></script>
    <script>
        $(document).on('click', '.reconcilation_done', function(){
            let _id = $(this).attr("data-id");
            let _token = $('meta[name=_token]').attr('content');
            let formData = new FormData();
            formData.append('_token',_token);
            formData.append('id',_id);
            $.ajax({
                url: '{{ route('banking_statement.done') }}',
                type:"POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success:function(response){
                    toastr.success(response.message);
                    location.reload();
                },
                error:function(response) {
                    toastr.error(response.message);
                }
            });
        });
    </script>
@endpush
