<div class="modal fade admin-query" id="create_cash_flow_account">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account.new_expense') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="create_cash_flow">
                    <div class="row">
                        @if (Settings('default_expense_account') == 0 || Settings('default_expense_account') == Null)
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label red_input" for="">{{ trans("account.set_default_expense_account_first_from_account_configuration") }}</label>
                                </div>
                            </div>
                        @else
                            <div class="col-xl-12">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for="">{{ __('account.Payment from Account') }} ({{ trans('account.cash_or_bank') }})*</label>
                                    <select class="primary_select mb-15 credit_account_id" name="credit_account_id" id="credit_account_id">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{$errors->first('')}}</span>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ trans('account.purpose') }}</label>
                                    <input id="purpose" name="purpose" class="primary_input_field" placeholder="{{ trans('account.purpose') }}" type="text" required>
                                    <span class="text-danger" id="name_error"></span>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{ trans('account.amount') }}</label>
                                    <input id="amount" name="amount" class="primary_input_field" placeholder="{{ trans('account.amount') }}" type="text" required>
                                    <span class="text-danger" id="code_error"></span>
                                </div>
                            </div>

                            <div class="col-lg-12 text-center">
                                <div class="d-flex justify-content-center">
                                    <button class="primary-btn semi_large2 fix-gr-bg mr-10"  type="submit"><i class="ti-check"></i>{{trans('account.save') }}</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
