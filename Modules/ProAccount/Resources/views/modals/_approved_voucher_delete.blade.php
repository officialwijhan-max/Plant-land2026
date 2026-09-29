<div class="modal fade deleteForm" id="{{isset($modal_id)?$modal_id:'deleteApprovedDeleteModal'}}" >
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('common.Delete') {{ $item_name }} </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h4>@lang('common.Are you Sure to execute this operation?')</h4>
                </div>
                <form id="{{isset($form_id)?$form_id:'approved_voucher_delete_form'}}">
                    <input type="hidden" name="id" id="{{isset($delete_item_id)?$delete_item_id:'delete_approved_item_id'}}">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('common.Password') }}</label>
                                <input id="password" name="password" class="primary_input_field" placeholder="{{ trans('common.Password') }}" type="password" required>
                                <span class="text-danger" id="password_error"></span>
                            </div>
                        </div>
                        <div class="col-xl-12 text-right">
                            <input id="{{isset($dataDeleteBtn)?$dataDeleteBtn:'dataDeleteBtn'}}" type="submit" class="primary-btn tr-bg text-right" value="Delete"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
