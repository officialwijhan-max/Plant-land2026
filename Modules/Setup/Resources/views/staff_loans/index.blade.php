@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('common.Loan Apply') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('setup::staff_loans.paginates.list')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="edit_form"></div>
    <input type="hidden" name="user_select_list_option" id="user_select_list_option" value="{{ route('user.select_all_user') }}">
    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'loan_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('common.Date') }}','date'],['3','{{ __('common.User') }}','user'],['4','{{ __('department.Department') }}','department'],['5','{{ __('common.Type') }}','type'],['6','{{ __('common.Amount') }}','amount'],['7','{{ __('common.Monthly Installment') }}','monthly_installment'],['8','{{ __('common.Due') }}','due'],['9','{{ __('common.Status') }}','status'],['10','{{ __('common.Action') }}','action']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['loan_list']) }}">

    @include('setup::staff_loans.create')
    @include('backEnd.partials.delete_modal')
@endsection
@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script src="{{ Module::asset('core:staff_select.js') }}"></script>
<script type="text/javascript">
    $("#ApplyLoan_addForm").on("submit", function (event) {
        event.preventDefault();
        let formData = $(this).serializeArray();
        $.each(formData, function (key, message) {
            $("#" + formData[key].name + "_error").html("");
        });
        $.ajax({
            url: "{{route("apply_loans.store")}}",
            data: formData,
            type: "POST",
            success: function (response) {
                $("#ApplyLoan").modal("hide");
                $("#ApplyLoan_addForm").trigger("reset");
                toastr.success(response.message, trans('js.Success'));
                location.reload();
            },
            error: function (error) {
                if (error) {
                    $.each(error.responseJSON.errors, function (key, message) {
                        $("#" + key + "_error").html(message[0]);
                    });
                }
            }

        });
    });

    function getMonthlyInstallment() {
        var loan_amount = checkNaN(parseFloat($('#amount').val()));
        var total_month = checkNaN(parseInt($('#total_month').val()));
        var monthly_installment = 0;
        monthly_installment = loan_amount / total_month;
        $("#monthly_installment").val(monthly_installment.toFixed(2));
        $("#monthly_installment").val(monthly_installment.toFixed(2));
    }

    function ApplyLoanEdit(el) {
        $.post('{{ route('apply_loans.edit') }}', {_token: '{{ csrf_token() }}', id: el}, function (data) {
            $('#edit_form').html(data);
            $('#ApplyLoanEdit').modal('show');
            $('select').niceSelect();
        });
    }

    function ApplyLoanView(el) {
        $.post('{{ route('apply_loans.show') }}', {_token: '{{ csrf_token() }}', id: el}, function (data) {
            $('#edit_form').html(data);
            $('#ApplyLoanview').modal('show');
        });
    }
</script>
@endpush
