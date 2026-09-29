<div class="modal fade admin-query" id="addBalanceModal">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{__('purchase.Add Balance')}}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ route('customer.voucher_recieve.store') }}" id="addBalanceModalForm">
                    @csrf
                    <div class="row">
                        <input type="hidden" id="voucher_type" name="voucher_type" value="">
                        <div class="col-xl-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{__('account.Date')}}</label>
                                <div class="primary_datepicker_input">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="">
                                                <input placeholder="{{__('account.Date')}}" class="primary_input_field primary-input date form-control" id="startDate" type="text" name="date" value="" autocomplete="off" required>
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
                            @if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro")
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.Payment To')}}</label>
                                    <input class="primary_input_field" name="debit_account_name" type="text" value="{{ $customer->morph->name }}" readonly>
                                    <input class="primary_input_field" name="debit_account_id[]" type="hidden" value="{{ $customer->morph->id }}" readonly>
                                    <span class="text-danger">{{$errors->first('debit_account_id')}}</span>
                                </div>
                            @else
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{__('contact.Balance Adding Account')}}</label>
                                    <input class="primary_input_field" name="credit_account_name" placeholder="{{__('contact.Substaction Balance From')}}}" type="text" value="{{ \Modules\Account\Entities\ChartAccount::where('contactable_type', 'Modules\Contact\Entities\ContactModel')->where('contactable_id', $customer->id)->first()->name }}" readonly>
                                    <input class="primary_input_field" name="credit_account_id" placeholder="{{__('contact.Substaction Balance From')}}}" type="hidden" value="{{ \Modules\Account\Entities\ChartAccount::where('contactable_type', 'Modules\Contact\Entities\ContactModel')->where('contactable_id', $customer->id)->first()->id }}" readonly>
                                    <span class="text-danger">{{$errors->first('credit_account_id')}}</span>
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> {{ __('account.Narration')}} </label>
                                <input class="primary_input_field" name="debit_account_narration[]" placeholder="{{ __('account.Narration')}}" type="text">
                                <span class="text-danger">{{$errors->first('debit_account_narration')}}</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> {{__('account.Amount')}} </label>
                                <input class="primary_input_field" name="debit_account_amount[]" placeholder="{{__('account.Amount')}}" type="number">
                                <span class="text-danger">{{$errors->first('debit_account_amount')}}</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{__('account.Recieve By')}}</label>
                                @if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro")
                                    <select class="select2 mb-15 single_select primary_singleSelect account_id" name="credit_account_id" id="credit_account_id" required>
                                        <option value="0">{{__('common.Select One')}}</option>
                                    </select>
                                    <span class="text-danger">{{$errors->first('credit_account_id')}}</span>
                                @else
                                    <select class="select2 mb-15 single_select primary_singleSelect account_id" name="debit_account_id" id="debit_account_id" required>
                                        <option value="0">{{__('common.Select One')}}</option>
                                    </select>
                                    <span class="text-danger">{{$errors->first('debit_account_id')}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6 cheque_no_div">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label"
                                       for="cheque_no"> {{__('account.Cheque Number')}} </label>
                                <input class="primary_input_field" name="cheque_no" id="cheque_no" placeholder="{{__('account.Cheque Number')}}" type="text" value="{{old('cheque_no')}}">
                                <span class="text-danger">{{$errors->first('cheque_no')}}</span>
                            </div>
                        </div>

                        <div class="col-lg-6 cheque_date_div">
                            <div class="primary_input mb-15">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{__('account.Cheque Date')}}</label>
                                    <div class="primary_datepicker_input">
                                        <div class="no-gutters input-right-icon">
                                            <div class="col">
                                                <div class="">
                                                    <input placeholder="{{__('account.Cheque Date')}}" class="primary_input_field primary-input date form-control" id="cheque_date" type="text" name="cheque_date" value="" autocomplete="off">
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
                                <label class="primary_input_label"
                                       for=""> {{__('account.Bank Name')}} </label>
                                <input class="primary_input_field" name="bank_name" id="bank_name" placeholder="{{__('account.Bank Name')}}" type="text" value="{{old('bank_name')}}">
                                <span class="text-danger">{{$errors->first('bank_name')}}</span>
                            </div>
                        </div>

                        <div class="col-lg-6 bank_branch_div">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> {{__('account.Bank Branch')}} </label>
                                <input class="primary_input_field" name="bank_branch" id="bank_branch" placeholder="{{__('account.Bank Branch')}}" type="text" value="{{old('bank_branch')}}">
                                <span class="text-danger">{{$errors->first('bank_branch')}}</span>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center pt_20">
                                <button type="submit" class="primary-btn semi_large2  fix-gr-bg"
                                        id="save_button_parent"><i
                                        class="ti-check"></i>{{ __('purchase.Add Balance') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
