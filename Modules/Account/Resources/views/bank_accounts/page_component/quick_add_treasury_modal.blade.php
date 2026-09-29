{{--
    Minimal quick-add-treasury (bank account) modal, meant to be
    @include()'d from any form with a bank/treasury picker (Add Expense,
    Add Revenue) so a new bank account doesn't require leaving the page.
    Reuses the same bank_accounts.store endpoint as the full Bank
    Accounts page.
--}}
<div class="modal fade admin-query" id="quickAddTreasuryModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('common.Add New') }} {{ __('account.Bank Accounts') }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="quick_add_treasury_form">
                    @csrf
                    <div class="primary_input mb-25">
                        <label class="primary_input_label">{{ trans('common.Bank Name') }} *</label>
                        <input name="bank_name" class="primary_input_field" type="text" required>
                        <span class="text-danger" id="quick_treasury_bank_name_error"></span>
                    </div>
                    <div class="primary_input mb-25">
                        <label class="primary_input_label">{{ __('common.Branch Name') }} *</label>
                        <input name="branch_name" class="primary_input_field" type="text" required>
                        <span class="text-danger" id="quick_treasury_branch_name_error"></span>
                    </div>
                    <div class="primary_input mb-25">
                        <label class="primary_input_label">{{ __('common.Account Number') }} *</label>
                        <input name="account_no" class="primary_input_field" type="text" required>
                        <span class="text-danger" id="quick_treasury_account_no_error"></span>
                    </div>
                    <input type="hidden" name="status" value="1">
                    <div class="text-center pt_20">
                        <button type="submit" class="primary-btn semi_large2 fix-gr-bg"><i class="ti-check"></i>{{ __('common.Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
