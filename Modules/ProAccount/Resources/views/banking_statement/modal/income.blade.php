<div class="modal fade admin-query" id="create_transaction">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('account.Recieved') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="create_cash_flow" method="POST" action="{{ route('banking_statement.transaction_entry_income') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{trans('account.date')}} *</label>
                                <div class="primary_datepicker_input">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="position_relative">
                                                <input class="primary_input_field primary-input custom-date form-control" id="startDate" type="text" name="date" value="{{date('m/d/Y')}}" autocomplete="off" required>
                                                <div class="custom_datepicker_design"></div>
                                            </div>
                                        </div>
                                        <button class="date-icon" type="button">
                                            <i class="ti-calendar"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{ __('account.Recieve Account') }} *</label>
                                <select class="primary_select mb-15 credit_account_id" name="credit_account_id" id="credit_account_id">
                                    <option value="{{ $response->banking_statement->leadger_id }}" selected>{{ $response->banking_statement->leadger->name }}</option>
                                </select>
                                <span class="text-danger">{{$errors->first('')}}</span>
                            </div>
                        </div>
                    </div>
                    <hr class="dashed">
                    <div class="entry_row_div">
                        <div class="row new_added_row">
                            <div class="col-md-6 upper_account_div">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{trans('account.select_category')}} *</label>
                                    <select class="select2 primary_singleSelect mb-15 account_id" data-row="1" name="account_id[]" required>
                                        <option value="0">{{ trans('account.Select one') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for=""> {{trans('account.narration')}}</label>
                                    <input class="primary_input_field" name="narration[]" id="narration" value="{{ $response->narration }}" type="text">
                                </div>
                            </div>
                            <div class="col-md-6 debit_amount_div">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for=""> {{trans('account.amount')}} *</label>
                                    <input class="primary_input_field debit_amount" name="debit_amount[]" id="debit_amount" value="{{ $response->amount }}" type="number" min="0" step="0.01">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{ trans('contact.Customer') }} / {{ trans('contact.Supplier') }}</label>
                                    <div class="sub_account_below_div">
                                        <select class="select2 mb-15 sub_account_id" data-rows="1" name="sub_account_id[]" id="sub_account_id">
                                            <option value="0">{{trans('account.Select one')}}</option>
                                        </select>
                                    </div>
                                    <span class="text-danger">{{$errors->first('sub_account_id')}}</span>
                                </div>
                            </div>
                            <div class="col-lg-12 text-center">
                                <div class="d-flex justify-content-center pt_20">
                                    <button type="submit" class="primary-btn semi_large2 submit_button_form fix-gr-bg" id="save_button_parent"><i
                                            class="ti-check"></i>{{ trans('common.Save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
