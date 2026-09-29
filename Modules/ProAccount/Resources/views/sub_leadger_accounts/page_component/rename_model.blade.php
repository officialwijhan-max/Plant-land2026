{{-- update modal --}}
<div class="modal fade admin-query" id="RenameAccount">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account.rename_account') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('leadger.rename_account') }}" method="post" id="chart_account_rename_form">
                    @csrf
                    <div class="row">
                        <input type="text" class="account_id d-none" name="account_id">
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="">{{ trans('common.Name') }}</label>
                                <input name="name" class="primary_input_field name" placeholder="Name" type="text" required>
                                <span class="text-danger" id="edit_name_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.code') }}</label>
                                <input name="code" class="primary_input_field code" placeholder="{{ trans('account.code') }}" type="text" required>
                                <span class="text-danger" id="edit_code_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12 leadger_account_dib">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{ trans('account.select_leadger_account') }}</label>
                                <div id="edit_leadger_account_list"></div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="primary_input">
                                <label class="primary_input_label" for="">{{ trans('account.status') }}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="bg_option" class="primary_checkbox d-flex mr-12">
                                            <input name="is_active" value="1" class="active" type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('common.Active')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option" class="primary_checkbox d-flex mr-12">
                                            <input name="is_active" value="0" class="de_active" type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('common.Inactive')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="edit_status_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center pt_20">
                                <button type="submit" class="primary-btn semi_large2 update_btn fix-gr-bg"
                                        id="save_button_parent"><i class="ti-check"></i>{{ trans('account.update') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
