
<!-- Add Modal Item_Details -->
<div class="modal fade admin-query" id="Item_Details">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account.add_new_leadger') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <form method="POST" id="chart_account_form">
                    <div class="row">

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.name') }}</label>
                                <input id="name" name="name" class="primary_input_field" placeholder="{{ trans('account.name') }}" type="text" required>
                                <span class="text-danger" id="name_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.code') }}</label>
                                <input id="code" name="code" class="primary_input_field" placeholder="{{ trans('account.code') }}" type="text" required>
                                <span class="text-danger" id="code_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12" class="account_type" id="account_type">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{trans('account.account_type')}}</label>
                                <select class="primary_select mb-25 a_type" name="type" id="type" required>
                                    <option value="1">{{trans('account.asset')}}</option>
                                    <option value="2">{{trans('account.liability')}}</option>
                                    <option value="3">{{trans('account.expense')}}</option>
                                    <option value="4">{{trans('account.income')}}</option>
                                    <option value="5">{{trans('account.equity')}}</option>
                                </select>
                                <span class="text-danger" id="type_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                        for="">{{ trans('account.description') }}</label>
                                <input name="description" class="primary_input_field" placeholder="{{ trans('account.description') }}" type="text">
                                <span class="text-danger" id="description_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input">
                                <label class="primary_input_label" for="">{{ trans('account.is_group_/_cost_center') }}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                                            <input name="is_cost_center" value="1" type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.yes')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option"
                                                class="primary_checkbox d-flex mr-12">
                                            <input name="is_cost_center" value="0" checked type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.no')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="is_group_error"></span>
                            </div>
                        </div>

                        <div class="col-md-6" id="acc_type_div">
                            <div class="primary_input">
                                <label class="primary_input_label"for="">{{trans('account.leadger_type')}}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="color_option"
                                                class="primary_checkbox d-flex mr-12">
                                            <input class="acc_type_others" id="" name="acc_type" value="others" checked type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.others')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                                            <input class="acc_type" name="acc_type" value="cash" type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.cash')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option"
                                                class="primary_checkbox d-flex mr-12">
                                            <input class="acc_type" name="acc_type" value="bank" type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.bank')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="acc_type_error"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.opening_balance') }}</label>
                                <input id="opening_balance" name="opening_balance" class="primary_input_field" placeholder="{{ trans('account.opening_balance') }}" type="number" step="0.0000001" value="0" required>
                                <span class="text-danger" id="code_error"></span>
                            </div>
                        </div>


                        <div class="col-xl-12">
                            <div class="primary_input">
                                <label class="primary_input_label"
                                        for="">{{ trans('account.status') }}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                                            <input name="is_active" value="1" checked type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('common.Active')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option"
                                                class="primary_checkbox d-flex mr-12">
                                            <input name="is_active" value="0" type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('common.Inactive')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="status_error"></span>
                            </div>
                        </div>
                        <input type="hidden" name="row_count" id="row_count" value="1">
                        <input type="hidden" name="class_name" id="class_name" value="0">
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.add_as_sub_account') }} </label>
                                <label data-id="color_option"
                                        class="primary_checkbox d-flex mr-12">
                                    <input name="as_sub_category" value="1" class="as_sub_category" type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-xl-12 parent_chartAccount" style="display: none;">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{ trans('account.select_parent_account') }} </label>
                                <div id="parent_chart_account_list"></div>
                            </div>
                        </div>

                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center pt_20">
                                <button type="submit" class="primary-btn semi_large2 submit_button_form fix-gr-bg" id="save_button_parent"><i class="ti-check"></i>{{ trans('account.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
