{{--
    Minimal quick-add-category modal, meant to be @include()'d from any
    form with a category/account picker (Add Expense, Add Revenue) so a
    new category doesn't require leaving the page.

    Reuses the same char_accounts.store endpoint as the full Chart of
    Accounts page, just with a smaller field set (name only) and `type`
    fixed via a hidden input to whatever context this was included from,
    rather than exposing the full type/is_group/cost-center/sub-account
    form meant for the standalone Chart of Accounts screen.

    Expected variables:
      $type    - ChartAccount type to create under (3 = Expense, 4 = Income)
      $modalId - unique id for this modal instance, since a page may
                 include this partial more than once
--}}
<div class="modal fade admin-query" id="{{ $modalId }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('common.Add New') }} {{ __('account.Category') }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" class="quick_add_category_form" data-modal-id="{{ $modalId }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="is_group" value="0">
                    <input type="hidden" name="status" value="1">
                    <div class="primary_input mb-25">
                        <label class="primary_input_label">{{ __('common.Name') }} *</label>
                        <input name="name" class="primary_input_field quick_add_category_name" type="text" required>
                        <span class="text-danger quick_add_category_error"></span>
                    </div>
                    <div class="text-center pt_20">
                        <button type="submit" class="primary-btn semi_large2 fix-gr-bg"><i class="ti-check"></i>{{ __('common.Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
