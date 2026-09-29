 @extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('common.Loan Approval') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('setup::approval_loans.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="edit_form"></div>
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'approval_loan_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('common.Date') }}','date'],['3','{{ __('common.User') }}','user'],['4','{{ __('department.Department') }}','department'],['5','{{ __('common.Type') }}','type'],['6','{{ __('common.Amount') }}','amount'],['7','{{ __('common.Monthly Installment') }}','monthly_installment'],['8','{{ __('common.Due') }}','due'],['9','{{ __('common.Status') }}','status'],['10','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['approval_loan_list']) }}">

@include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script type="text/javascript">
        function ApplyLoanDetails(el){
            $.post('{{ route('applied_loans.show') }}', {_token:'{{ csrf_token() }}', id:el}, function(data){
                $('#edit_form').html(data);
                $('#ApplyLoanview').modal('show');
                $('select').niceSelect();
            });
        }
    </script>
@endpush
