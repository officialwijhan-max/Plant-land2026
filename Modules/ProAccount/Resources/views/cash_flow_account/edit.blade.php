<div class="modal fade admin-query" id="edit_cash_flow_modal">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account.update_cash_flow_account') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="update_cash_flow">
                    <input type="hidden" id="rowId" value="{{$row->id}}">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.name') }}</label>
                                <input id="name" name="name" class="primary_input_field" value="{{$row->name}}" placeholder="{{ trans('account.name') }}" type="text" required>
                                <span class="text-danger" id="name_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.code') }}</label>
                                <input id="code" name="code" class="primary_input_field" value="{{$row->code}}" placeholder="{{ trans('account.code') }}" type="text" required>
                                <span class="text-danger" id="code_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input">
                                <label class="primary_input_label"
                                       for="">{{ trans('account.account_type') }}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                                            <input name="type" value="3" @if ($row->type == 3) checked @endif type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.expense')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option"
                                               class="primary_checkbox d-flex mr-12">
                                            <input name="type" value="4" @if ($row->type == 4) checked @endif type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.income')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="type_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input">
                                <label class="primary_input_label"
                                       for="">{{ trans('common.Status') }}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                                            <input name="is_active" value="1" @if ($row->is_active == 1) checked @endif type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('common.Active')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option"
                                               class="primary_checkbox d-flex mr-12">
                                            <input name="is_active" value="0" @if ($row->is_active == 0) checked @endif type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('common.Inactive')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="status_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center">
                                <button class="primary-btn semi_large2  fix-gr-bg mr-10"  type="submit"><i class="ti-check"></i>{{trans('account.update') }}</button>
                                <button class="primary-btn semi_large2  fix-gr-bg" id="save_button_parent" data-dismiss="modal" type="button"><i class="ti-check"></i>{{trans('common.Cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
