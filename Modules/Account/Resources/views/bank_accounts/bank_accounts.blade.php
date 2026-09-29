@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('account.Bank Accounts') }}</h3>

                            <ul class="d-flex">
                                @if(permissionCheck('bank_accounts.store'))
                                <li><a data-toggle="modal" data-target="#Item_Details"
                                       class="primary-btn radius_30px mr-10 fix-gr-bg" href="#">
                                        <i class="ti-plus"></i>{{ __('common.Add New') }} {{ __('account.Bank Accounts') }}</a></li>
                                @endif
                                    @if(permissionCheck('bank_account.csv_upload.store'))
                                <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route('bank_account.csv_upload.create')}}"><i class="ti-export"></i>{{__('common.Upload Via CSV')}}</a></li>
                                    @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="">
                                <div id="item_list_tbl">
                                    @include('account::bank_accounts.page_component.list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- create & update modal --}}
                @include('account::bank_accounts.page_component.create_modal')
                @include('account::bank_accounts.page_component.udpate_modal')
            </div>
        </div>
    </section>

    <div class="showModalHideColumn"></div>
    @php
        $employee_per = auth()->user()->user_col_permissions->where('table_name', 'bank_account_list')->first();
    @endphp
    @if ($employee_per)
        @if ($employee_per->hide_column_no_by_self)
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
        @endif
    @else
        <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
    @endif
    <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.ID')}}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('common.Bank Branch Name') }}','bank_branch_name'],['4','{{ __('common.Account Name') }}','account_name'],['5','{{ __('common.Bank Account Number') }}','account_num'],['6','{{ __('account.Balance') }}','balance'],['7','{{ __('common.Status') }}','status']]">
    <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['bank_account_list']) }}">
    
    @include('backEnd.partials.delete_modal')
@endsection

@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script type="text/javascript">
     var baseUrl = $('#app_base_url').val();
        $(document).ready(function () {

            $(".as_sub_category").unbind().on('click', function () {
                $(".parent_chartAccount").toggle();
                $("#account_type").toggle();
            });


            $("#chart_account_form").on("submit", function (event) {
                event.preventDefault();
                let formData = $(this).serializeArray();
                $.each(formData, function (key, message) {
                    $("#" + formData[key].name + "_error").html("");
                });
                $.ajax({
                    url: "{{route("bank_accounts.store")}}",
                    data: formData,
                    type: "POST",
                    success: function (response) {
                        $("#Item_Details").modal("hide");
                        $("#chart_account_form").trigger("reset");
                        $(".parent_chartAccount").hide();
                        chartAccountList();
                        toastr.success(response.success,"Success");
                    },
                    error: function (error) {
                        if (error) {
                            $.each(error.responseJSON.errors, function (key, message) {
                                $("#" + key + "_error").html(message[0]);
                            });
                        }
                        toastr.warning("Something went wrong");
                    }

                });
            });


            $("#item_list_tbl").on("click", ".edit_chart_account", function () {
                let account = $(this).data("value");
                $(".edit_id").val(account.id);
                $(".bank_name").val(account.bank_name);
                $(".branch_name").val(account.branch_name);
                $(".account_name").val(account.account_name);
                $(".account_no").val(account.account_no);
                $(".description").val(account.description);
                $(".opening_balance").val(account.opening_balance);
                if (account.chart_account.status == 1) {
                    $(".active").attr("checked", true);
                } else {
                    $(".de_active").attr("checked", true);
                }
            });

            $(document).on("submit", "#ChartAccountEditForm", function (event) {
                event.preventDefault();
                let id = $(".edit_id").val();
                let formData = $(this).serializeArray();
                $.each(formData, function (key, message) {
                    $("#edit_" + formData[key].name + "_error").html("");
                });
                $.ajax({
                    url: "{{route('bank.account.update')}}",
                    data: formData,
                    type: "POST",
                    dataType: "JSON",
                    success: function (response) {
                        $("#ChartAccount_Edit").modal("hide");
                        $("#ChartAccountEditForm").trigger("reset");
                        $(".active").attr("checked", false);
                        $(".de_active").attr("checked", false);
                        toastr.success(response.success,"Success");
                        chartAccountList();
                    },
                    error: function (error) {
                        if (error) {
                            $.each(error.responseJSON.errors, function (key, message) {
                                $("#edit_" + key + "_error").html(message[0]);
                            });
                        }
                    }
                });
            });

            function chartAccountList() {
                $.ajax({
                    url: "{{route("bank_accounts.index")}}",
                    type: "GET",
                    dataType: "HTML",
                    success: function (response) {
                        $("#item_list_tbl").html(response);
                        $('.select_div').niceSelect();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }


        });

    </script>
@endpush
