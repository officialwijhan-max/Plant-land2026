@extends('backEnd.master')
@section('mainContent')

    <div id="add_payment">
        <section class="admin-visitor-area up_st_admin_visitor">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{ __('common.Add New') }} {{ __('inventory.Expense') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="white_box_50px box_shadow_white">
                            <!-- Prefix  -->
                            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" id="expense_create_form" onsubmit="if(this.dataset.submitted){return false;}this.dataset.submitted=true;document.getElementById('expense_save_btn').setAttribute('disabled','disabled');return true;">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Date')}} *</label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="">
                                                            <input placeholder="{{ __('account.Date') }}" value="{{date('m/d/Y')}}"
                                                                   class="primary_input_field primary-input date form-control"
                                                                   id="startDate" type="text" name="date"
                                                                   autocomplete="off" required>
                                                        </div>
                                                    </div>
                                                    <button class="" type="button">
                                                        <i class="ti-calendar" id="start-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Payment From')}} *</label>
                                            <select class="primary_select mb-15" name="voucher_type" id="payment_from_type" onchange="get_accounts()" required>
                                                @foreach ($account_categories as $key => $account_category)
                                                    <option value="{{ $account_category->id }}">{{ $account_category->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{$errors->first('voucher_type')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Payment From Account')}} *</label>
                                            <select class="select2 mb-15 single_select primary_singleSelect payment_from" name="credit_account_id" required>
                                                <option>{{__('common.Select One')}}</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('credit_account_id')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> {{__("account.Narration")}} </label>
                                            <input class="primary_input_field" name="narration"
                                                   placeholder="{{__("account.Narration")}}" type="text"
                                                   value="{{ old('narration') }}">
                                            <span class="text-danger">{{$errors->first('narration')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 cheque_no_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> {{__('account.Cheque Number')}} *</label>
                                            <input class="primary_input_field" name="cheque_no" id="cheque_no"
                                                   placeholder="{{ __('account.Cheque No') }}" type="text"
                                                   value="{{old('cheque_no')}}" required>
                                            <span class="text-danger">{{$errors->first('cheque_no')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 cheque_date_div">
                                        <div class="primary_input mb-15">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{__('account.Cheque Date')}}
                                                    *</label>
                                                <div class="primary_datepicker_input">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="">
                                                                <input placeholder="{{ __('account.Cheque Date') }}"
                                                                       class="primary_input_field primary-input date form-control"
                                                                       id="cheque_date" type="text" name="cheque_date"
                                                                       value="{{date('m/d/Y')}}" autocomplete="off"
                                                                       required>
                                                            </div>
                                                        </div>
                                                        <button class="" type="button">
                                                            <i class="ti-calendar" id="start-date-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 bank_name_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Bank Name')}} *
                                                <a href="javascript:" data-toggle="modal" data-target="#quickAddTreasuryModal" title="{{ __('account.Add New Treasury') }}"><i class="ti-plus"></i></a>
                                            </label>
                                            <select class=" mb-15" name="bank_name" id="bank_name" required>
                                                <option value="">Select Bank</option>
                                                @foreach ($bank_accounts as $key => $bank_detail)
                                                    <option data-id="{{ $bank_detail->id }}" value="{{ $bank_detail->bank_name }}">{{ $bank_detail->bank_name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{$errors->first('bank_name')}}</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="bank_id" id="bank_id" value="">

                                    <div class="col-lg-6 bank_branch_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{__('account.Bank Branch')}}
                                                *</label>
                                                <select class=" mb-15"  name="bank_branch" id="bank_branch" required>
                                                    <option value="">Select Branch</option>
                                                    @foreach ($bank_accounts as $key => $bank_detail)
                                                        <option value="{{ $bank_detail->branch_name }}">{{ $bank_detail->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                            <span class="text-danger">{{$errors->first('bank_branch')}}</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <label class="primary_input_label text-center" for=""
                                       id="dynamic_text">{{ __('account.Please Enter Expense details') }}</label>
                                <label class="h1 primary_input_label text-center gradient-color2" for=""
                                       id="alert_txt"></label>
                                <hr>
                                <div class="row form">
                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Payment To')}} *
                                                <a href="javascript:" data-toggle="modal" data-target="#quickAddExpenseCategory" title="{{ __('account.Add New Category') }}"><i class="ti-plus"></i></a>
                                            </label>
                                            <select class="select2 mb-15 single_select primary_singleSelect payment_to" name="sub_account_id[]">
                                                <option value="0">{{__('account.Select one')}}</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('sub_account_id.*')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Customer')}} </label>
                                            <select class="select2 mb-15 single_select primary_singleSelect customer_to" name="customer_id[]">
                                                <option value="0">{{__('account.Select one')}}</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('customer_id.*')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{__('account.Amount')}}*</label>
                                            <input class="primary_input_field" name="sub_amount[]"
                                                   placeholder="{{ __('account.Amount') }}" type="number" step="0.01">
                                            <span class="text-danger">{{$errors->first('sub_amount')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> {{__('account.Description')}} </label>
                                            <input class="primary_input_field" name="description[]"
                                                   placeholder="{{__('account.Description')}}" type="text">
                                            <span class="text-danger">{{$errors->first('amount')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> {{__('account.Narration')}} </label>
                                            <input class="primary_input_field" name="sub_narration[]"
                                                   placeholder="{{ __('account.Narration') }}" type="text">
                                            <span class="text-danger">{{$errors->first('amount')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-1">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{__('common.Action')}} </label>
                                            <button class="primary-btn btn-sm fix-gr-bg" id="add_payment_to_form"><i
                                                    class="ti-plus"></i>{{__('account.Add')}}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> {{__('account.Total Amount')}} </label>
                                            <input class="primary_input_field" name="sub_amounts" id="sub_amounts"
                                                   value="0" type="text" readonly>
                                            <span class="text-danger">{{$errors->first('sub_amounts')}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="submit_btn text-center ">
                                            <button class="primary-btn semi_large2 fix-gr-bg" id="expense_save_btn"><i
                                                    class="ti-check"></i>{{__("common.Save")}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <input type="hidden" name="expense_account_select_list_option" id="expense_account_select_list_option" data-type="3" value="{{ route('account.select_based_on_type') }}">
    <input type="hidden" name="get_customers" id="get_customers" data-type="3" value="{{ route('account.getcustomers') }}">
    <input type="hidden" name="select_based_on_configuration_group" id="select_based_on_configuration_group" value="{{ route('account.select_based_on_configuration_group') }}">

    @include('account::chart_accounts.page_component.quick_add_category_modal', ['type' => 3, 'modalId' => 'quickAddExpenseCategory'])
    @include('account::bank_accounts.page_component.quick_add_treasury_modal')
@endsection

@push("scripts")
<script src="{{ Module::asset('core:expense_accounts.js') }}"></script>
    <script type="text/javascript">
        setInterval(function () {
            sum_amount();
        }, 3000);
        $(document).ready(function () {
            hideDiv();
            get_accounts();
        });

        function sum_amount() {
            var sub_amounts = $("input[name='sub_amount[]']").map(function () {
                return $(this).val();
            }).get();
            var sum = 0;
            var sum = 0;
            for (var i = 0; i < sub_amounts.length; i++) {
                let sub_amount = parseFloat(sub_amounts[i]);
                if (isNaN(sub_amount)){
                    sub_amount = 0;
                }
                sum = sum + sub_amount;
            }

            $("#sub_amounts").val(sum);
        }

        function get_accounts() {
            var account_cat_id = $('#payment_from_type').val();
            $('.payment_from').append($('<option>', {value: 0,text: 'Select One'}));
            $('.payment_from').val(0);
            $('.payment_from').trigger("change");
            if (account_cat_id == 1) {
                $("#bank_branch").attr('disabled', true);
                $("#bank_name").attr('disabled', true);
                $("#cheque_date").attr('disabled', true);
                $("#cheque_no").attr('disabled', true);
                hideDiv();
            } else if (account_cat_id == 2) {
                $("#bank_branch").removeAttr("disabled");
                $("#bank_name").removeAttr("disabled");
                $("#cheque_date").removeAttr("disabled");
                $("#cheque_no").removeAttr("disabled");
                showDiv();
            }
        }

        function hideDiv() {
            $('.cheque_no_div').hide();
            $('.cheque_date_div').hide();
            $('.bank_name_div').hide();
            $('.bank_branch_div').hide();
        }

        function showDiv() {
            $('.cheque_no_div').show();
            $('.cheque_date_div').show();
            $('.bank_name_div').show();
            $('.bank_branch_div').show();
        }

        var i = 0;
        $(document).on('click', '#add_payment_to_form', function (e) {
            i++;
            e.preventDefault();
            $(".form").append('<div class="col-lg-2 row_id_' + i + '">' +
                '<div class="primary_input mb-15">' +
                '<select class="select2 mb-15 single_select primary_singleSelect payment_to" name="sub_account_id[]">' +
                '<option>'+ trans('js.Select One') +'</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-2 row_id_' + i + '">' +
                '<div class="primary_input mb-15">' +
                '<select class="select2 mb-15 single_select primary_singleSelect customer_to" name="customer_id[]">' +
                '<option>'+ trans('js.Select One') +'</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-2 row_id_' + i + '">' +
                '<div class="primary_input mb-15">' +
                '<input class="primary_input_field" name="sub_amount[]" placeholder="' + trans('js.Amount') + '" type="number" value="0"  step="0.01">' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-3 row_id_' + i + '">' +
                '<div class="primary_input mb-15">' +
                '<input class="primary_input_field" name="description[]" placeholder="' + '' + '" type="text" value="">' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-2 row_id_' + i + '">' +
                '<div class="primary_input mb-15">' +
                '<input class="primary_input_field" name="sub_narration[]" placeholder="' + trans('js.Narration') + '" type="text" value="">' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-1 row_id_' + i + '">' +
                '<div class="primary_input mb-15">' +
                '<button class="primary-btn btn-sm fix-gr-bg delete_payment_to_form" data-status="' + i + '"><i class="ti-trash"></i>' + trans('js.Delete') + '</button>' +
                '</div>' +
                '</div>');
                getAccountBasedOnType();
                getCustomers();
        });

        $(document).on('click', '.delete_payment_to_form', function (e) {
            e.preventDefault();
            var i = $(this).data('status');
            $('.row_id_' + i).remove();
        });
    </script>
   <script>
        $(document).ready(function () {
            // Initialize Select2
            const bankNameSelect = $('#bank_name').select2();
            const bankBranchSelect = $('#bank_branch').select2();
            const bankIdInput = $('#bank_id');

            // Store bank branches keyed by bank name
            const bankData = {};
            @foreach ($bank_accounts as $bank_detail)
                bankData["{{ $bank_detail->bank_name }}"] = {
                    id: "{{ $bank_detail->id }}",
                    branch: "{{ $bank_detail->branch_name }}"
                };
            @endforeach

            // Disable branch dropdown initially
            bankBranchSelect.prop('disabled', true).select2();

            // Handle bank name change event
            bankNameSelect.on('change', function () {
                const selectedBank = $(this).val();

                if (selectedBank && bankData[selectedBank]) {
                    // Clear and set branch dropdown value
                    bankBranchSelect
                        .empty()
                        .append(new Option(bankData[selectedBank].branch, bankData[selectedBank].branch, true, true))
                        .trigger('change')
                        .prop('disabled', true);

                    // Set hidden input bank_id
                    bankIdInput.val(bankData[selectedBank].id);
                } else {
                    // Reset branch dropdown if no valid bank is selected
                    bankBranchSelect
                        .empty()
                        .append(new Option('Select Branch', '', true, true))
                        .trigger('change')
                        .prop('disabled', false);

                    bankIdInput.val('');
                }
            });

            // Quick-add category - reuses the Chart of Accounts store
            // endpoint. The category dropdown is select2/AJAX-backed and
            // now sorted newest-first, so it just needs a moment before
            // the new entry shows up in results - no local list to sync.
            $(document).on('submit', '.quick_add_category_form', function (e) {
                e.preventDefault();
                var $form = $(this);
                var modalId = $form.data('modal-id');
                $.ajax({
                    url: "{{ route('char_accounts.store') }}",
                    type: "POST",
                    data: $form.serialize(),
                    success: function (response) {
                        $('#' + modalId).modal('hide');
                        $form.trigger('reset');
                        toastr.success(response.message || 'Category added');
                    },
                    error: function (error) {
                        $form.find('.quick_add_category_error').html('');
                        if (error.responseJSON && error.responseJSON.errors) {
                            $.each(error.responseJSON.errors, function (key, message) {
                                $form.find('.quick_add_category_error').html(message[0]);
                            });
                        } else if (error.responseJSON && error.responseJSON.message) {
                            $form.find('.quick_add_category_error').html(error.responseJSON.message);
                        } else {
                            toastr.warning('Something went wrong');
                        }
                    }
                });
            });

            // Quick-add treasury (bank account) - #bank_name is a plain
            // select2 populated server-side, not AJAX-backed, so the new
            // option is appended here directly and auto-selected.
            $(document).on('submit', '#quick_add_treasury_form', function (e) {
                e.preventDefault();
                var $form = $(this);
                $.ajax({
                    url: "{{ route('bank_accounts.store') }}",
                    type: "POST",
                    data: $form.serialize(),
                    success: function (response) {
                        $('#quickAddTreasuryModal').modal('hide');
                        var newBankName = $form.find('[name="bank_name"]').val();
                        var newBranchName = $form.find('[name="branch_name"]').val();
                        bankData[newBankName] = { id: response.id || '', branch: newBranchName };
                        bankNameSelect.append(new Option(newBankName, newBankName, false, false));
                        bankNameSelect.val(newBankName).trigger('change');
                        $form.trigger('reset');
                        toastr.success(response.success || 'Treasury added');
                    },
                    error: function (error) {
                        $('#quick_treasury_bank_name_error, #quick_treasury_branch_name_error, #quick_treasury_account_no_error').html('');
                        if (error.responseJSON && error.responseJSON.errors) {
                            $.each(error.responseJSON.errors, function (key, message) {
                                $('#quick_treasury_' + key + '_error').html(message[0]);
                            });
                        } else {
                            toastr.warning('Something went wrong');
                        }
                    }
                });
            });
        });




    </script>


    
@endpush
