@extends('backEnd.master')
@section('mainContent')
    @php
        $account_type = $expense->voucher->transactions->last();
        $dedit_accounts = $expense->voucher->transactions->where('is_customer', 0)->where('type', 'Dr');
        $credit_account = $expense->voucher->transactions->where('is_customer', 0)->where('type', 'Cr')->first();
        $document = $expense->voucher->document;
    @endphp
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{__("account.Update Expense Info")}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <!-- Prefix  -->
                        <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{__('account.Date')}} *</label>
                                        <div class="primary_datepicker_input">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="">
                                                        <input placeholder="Date" class="primary_input_field primary-input date form-control" id="startDate" type="text" name="date" value="{{ date('m/d/Y', strtotime($expense->voucher->date)) }}" autocomplete="off" required>
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
                                                <option value="{{ $account_category->id }}" @selected($credit_account->account->configuration_group_id == $account_category->id)>{{ $account_category->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{$errors->first('voucher_type')}}</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{__('account.Payment From Account')}} *</label>
                                        <select class="select2 mb-15 single_select primary_singleSelect payment_from" name="credit_account_id" required>
                                            <option value="{{ $credit_account->account_id }}" selected>{{ $credit_account->account->name }}</option>
                                        </select>
                                        <span class="text-danger">{{$errors->first('credit_account_id')}}</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                                for=""> {{__("account.Narration")}} </label>
                                        <input class="primary_input_field" name="narration" placeholder="{{__("account.Narration")}}" type="text" value="{{ $expense->voucher->narration }}">
                                        <span class="text-danger">{{$errors->first('narration')}}</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 cheque_no_div @if(!$document) d-none @endif">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                               for=""> {{__('account.Cheque Number')}} *</label>
                                        <input class="primary_input_field" name="cheque_no" id="cheque_no"
                                               placeholder="{{ __('account.Cheque No') }}" type="text"
                                               value="{{$document ? $document->cheque_no : ''}}" @if(!$document) disabled @else required @endif>
                                        <span class="text-danger">{{$errors->first('cheque_no')}}</span>
                                    </div>
                                </div>

                                <div class="col-lg-6 cheque_date_div @if(!$document) d-none @endif">
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
                                                                   value="{{$document ? date('m/d/Y', strtotime($document->cheque_date)) : date('m/d/Y')}}" autocomplete="off" @if(!$document) disabled @else required @endif>
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

                                <div class="col-lg-6 bank_name_div @if(!$document) d-none @endif">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                               for=""> {{__('account.Bank Name')}} *</label>
                                               <select class=" mb-15" name="bank_name" id="bank_name" required>
                                                    <option value="" disabled {{ !$document ? 'selected' : '' }}>Select Bank</option>
                                                    @foreach ($bank_accounts as $bank_detail)
                                                        <option value="{{ $bank_detail->bank_name }}" 
                                                            {{ $document && $document->bank_name == $bank_detail->bank_name ? 'selected' : '' }}>
                                                            {{ $bank_detail->bank_name }}
                                                        </option>
                                                    @endforeach
                                                </select>                                         
                                        {{-- <input class="primary_input_field" name="bank_name" id="bank_name"
                                               placeholder="{{ __('account.Bank Name') }}" type="text" value="{{$document ? $document->bank_name : ''}}" @if(!$document) disabled @else required @endif> --}}
                                        <span class="text-danger">{{$errors->first('bank_name')}}</span>
                                    </div>
                                </div>
                                <input type="hidden" name="bank_id" id="bank_id" value="">
                                <div class="col-lg-6 bank_branch_div @if(!$document) d-none @endif">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('account.Bank Branch')}}
                                            *</label>
                                            <select class=" mb-15" name="bank_branch" id="bank_branch" required>
                                                <option value="" disabled {{ !$document ? 'selected' : '' }}>Select Bank</option>
                                                @foreach ($bank_accounts as $bank_detail)
                                                    <option value="{{ $bank_detail->branch_name }}" 
                                                        {{ $document && $document->bank_branch == $bank_detail->branch_name ? 'selected' : '' }}>
                                                        {{ $bank_detail->branch_name }}
                                                    </option>
                                                @endforeach
                                            </select>   
                                        {{-- <input class="primary_input_field" name="bank_branch" id="bank_branch"
                                               placeholder="{{ __('account.Bank Branch') }}" type="text"
                                               value="{{$document ? $document->bank_branch : ''}}" @if(!$document) disabled @else required @endif> --}}
                                        <span class="text-danger">{{$errors->first('bank_branch')}}</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <label class="primary_input_label text-center" for="" id="dynamic_text">{{ __('account.Please Enter Expense details') }}</label>
                            <label class="h1 primary_input_label text-center gradient-color2" for="" id="alert_txt"></label>
                            <hr>
                            <div class="row form">
                                <div class="col-lg-2">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{__('account.Payment To')}} *</label>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('account.Customer')}}</label>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('account.Amount')}} *</label>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('account.Description')}} </label>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('account.Narration')}} </label>
                                    </div>
                                </div>

                                <div class="col-lg-1">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('common.Action')}} </label>
                                    </div>
                                </div>
                                @foreach ($dedit_accounts as $key => $dedit_account)
                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <select class="select2 mb-15 single_select primary_singleSelect payment_to" name="sub_account_id[]" required>
                                                <option value="{{ $dedit_account->account_id }}" selected>{{ $dedit_account->account->name }}</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('payment_from')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <select class="select2 mb-15 single_select primary_singleSelect customer_to" name="customer_id[]" required>
                                                <option value="{{ $dedit_account->customer_id }}" selected>{{ $dedit_account->customer->name }}</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('payment_from')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <input class="primary_input_field" name="sub_amount[]" placeholder="{{ __('account.Amount') }}" type="number" value="{{ $dedit_account->amount }}">
                                            <span class="text-danger">{{$errors->first('sub_amount')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="primary_input mb-15">
                                            <input class="primary_input_field" name="description[]" placeholder="{{__('account.Description')}}" type="text" value="{{ $dedit_account->description }}">
                                            <span class="text-danger">{{$errors->first('amount')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="primary_input mb-15">
                                            <input class="primary_input_field" name="sub_narration[]" placeholder="{{ __('account.Narration') }}" type="text" value="{{ $dedit_account->narration }}">
                                            <span class="text-danger">{{$errors->first('amount')}}</span>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-lg-1">
                                    <div class="primary_input mb-15">
                                        <button class="primary-btn btn-sm fix-gr-bg" id="add_payment_to_form"><i class="ti-plus"></i>{{__('account.Add')}}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('account.Total Amount')}} </label>
                                        <input class="primary_input_field" name="sub_amounts" id="sub_amounts" type="text" value="{{ $expense->voucher->amount }}" readonly>
                                        <span class="text-danger">{{$errors->first('amount')}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="submit_btn text-center ">
                                        <button class="primary-btn semi_large2 fix-gr-bg"><i class="ti-check"></i>{{__("common.Update")}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" name="expense_account_select_list_option" id="expense_account_select_list_option" data-type="3" value="{{ route('account.select_based_on_type') }}">
    <input type="hidden" name="get_customers" id="get_customers" data-type="3" value="{{ route('account.getcustomers') }}">
    <input type="hidden" name="select_based_on_configuration_group" id="select_based_on_configuration_group" value="{{ route('account.select_based_on_configuration_group') }}">
@endsection

@push("scripts")
<script src="{{ Module::asset('core:expense_accounts.js') }}"></script>
    <script type="text/javascript">
        setInterval(function() {
            sum_amount();
        }, 3000);

        function sum_amount()
        {
            var sub_amounts = ($("input[name='sub_amount[]']").map(function(){return $(this).val();}).get());

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
                hideDiv();
            } else if (account_cat_id == 2) {
                showDiv();
            }
        }

        function hideDiv() {
            $("#bank_branch").attr('disabled', true);
            $("#bank_name").attr('disabled', true);
            $("#cheque_date").attr('disabled', true);
            $("#cheque_no").attr('disabled', true);
            $('.cheque_no_div').addClass('d-none');
            $('.cheque_date_div').addClass('d-none');
            $('.bank_name_div').addClass('d-none');
            $('.bank_branch_div').addClass('d-none');
        }

        function showDiv() {
            $("#bank_branch").removeAttr("disabled");
            $("#bank_name").removeAttr("disabled");
            $("#cheque_date").removeAttr("disabled");
            $("#cheque_no").removeAttr("disabled");
            $('.cheque_no_div').removeClass('d-none');
            $('.cheque_date_div').removeClass('d-none');
            $('.bank_name_div').removeClass('d-none');
            $('.bank_branch_div').removeClass('d-none');
        }

        var i = 0;
        $(document).on('click', '#add_payment_to_form', function(e){
            i++;
            e.preventDefault();
            $( ".form" ).append('<div class="col-lg-2 row_id_'+i+'">'+
                '<div class="primary_input mb-15">' +
                '<select class="select2 mb-15 single_select primary_singleSelect payment_to" name="sub_account_id[]" id="" required>' +
                '<option>'+ trans('js.Select One') +'</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-2 row_id_'+i+'">'+
                '<div class="primary_input mb-15">' +
                '<select class="select2 mb-15 single_select primary_singleSelect customer_to" name="customer_id[]" id="" required>' +
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
            '</div>' );
            getAccountBasedOnType();
            getCustomers();
        });

        $(document).on('click', '.delete_payment_to_form', function(e){
            e.preventDefault();
            var i = $(this).data('status');
            $('.row_id_'+i).remove();
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
        });



       
    </script>
@endpush
