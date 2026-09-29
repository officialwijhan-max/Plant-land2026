<div class="modal fade admin-query" id="edit_cash_flow_modal">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account.expense_edit') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            @php
                $credit_account = $row->transactions->where('type', 'Cr')->first();
                $dedit_account = $row->transactions->where('type', 'Dr')->first();
            @endphp
            <div class="modal-body">
                <form id="update_cash_flow">
                    <input type="hidden" id="rowId" value="{{$row->id}}">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{ __('account.Payment from Account') }} ({{ trans('account.cash_or_bank') }})*</label>
                                <select class="primary_select mb-15 credit_account_id" name="credit_account_id" id="credit_account_id">
                                    <option value="{{ $credit_account->leadger_id }}" selected>{{ $credit_account->leadger->name }}</option>
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
                                <input id="purpose" name="purpose" class="primary_input_field" value="{{$row->narration}}" placeholder="{{ trans('account.purpose') }}" type="text" required>
                                <span class="text-danger" id="purpose_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.amount') }}</label>
                                <input id="amount" name="amount" class="primary_input_field" value="{{$row->amount}}" placeholder="{{ trans('account.amount') }}" type="text" required>
                                <span class="text-danger" id="amount_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center">
                                <button class="primary-btn semi_large2 fix-gr-bg mr-10"  type="submit"><i class="ti-check"></i>{{trans('account.update') }}</button>
                                <button class="primary-btn semi_large2 fix-gr-bg" id="save_button_parent" data-dismiss="modal" type="button"><i class="ti-check"></i>{{trans('common.Cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
