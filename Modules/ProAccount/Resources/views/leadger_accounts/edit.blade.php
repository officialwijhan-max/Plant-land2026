{{-- update modal --}}
<div class="modal fade admin-query" id="RenameAccount">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('common.Edit') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('leadger.rename_account') }}" method="post" id="chart_account_rename_form">
                    @csrf
                    <div class="row">
                        <input type="text" class="account_id d-none" name="account_id" value="{{ $leadger->id }}">
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.name') }}</label>
                                <input name="name" class="primary_input_field name" placeholder="{{ trans('account.name') }}" value="{{ $leadger->name }}" type="text" required>
                                <span class="text-danger" id="edit_name_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.code') }}</label>
                                <input name="code" class="primary_input_field code" placeholder="{{ trans('account.code') }}" value="{{ $leadger->code }}" type="text" required>
                                <span class="text-danger" id="edit_code_error"></span>
                            </div>
                        </div>
                        @if ($leadger->is_blocked == 0 && $leadger->id > 60)
                            <div class="col-xl-12" class="account_type" id="account_type">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{trans('account.account_type')}}</label>
                                    <select class="primary_select mb-25 a_type" name="type" id="type" required>
                                        <option value="1" @if ($leadger->type == 1) selected @endif>{{trans('account.asset')}}</option>
                                        <option value="2" @if ($leadger->type == 2) selected @endif>{{trans('account.liability')}}</option>
                                        <option value="3" @if ($leadger->type == 3) selected @endif>{{trans('account.expense')}}</option>
                                        <option value="4" @if ($leadger->type == 4) selected @endif>{{trans('account.income')}}</option>
                                        <option value="5" @if ($leadger->type == 5) selected @endif>{{trans('account.equity')}}</option>
                                    </select>
                                    <span class="text-danger" id="type_error"></span>
                                </div>
                            </div>
                        @endif
                        

                        <div class="col-xl-12" id="acc_type_div_edit">
                            <div class="primary_input">
                                <label class="primary_input_label"for="">{{trans('account.leadger_type')}}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="color_option"
                                               class="primary_checkbox d-flex mr-12">
                                            <input class="acc_type_others_edit" id="" name="acc_type" value="others" checked type="radio" @if ($leadger->acc_type == "others") checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.others')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="bg_option" class="primary_checkbox d-flex mr-12 ">
                                            <input class="acc_type_cash" name="acc_type" value="cash" type="radio" @if ($leadger->acc_type == "cash") checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.cash')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option"
                                               class="primary_checkbox d-flex mr-12">
                                            <input class="acc_type_bank" name="acc_type" value="bank" type="radio" @if ($leadger->acc_type == "bank") checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('account.bank')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="acc_type_error"></span>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="primary_input">
                                <label class="primary_input_label" for="">{{ trans('common.Status') }}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="bg_option" class="primary_checkbox d-flex mr-12">
                                            <input name="is_active" value="1" class="active" type="radio" @if ($leadger->is_active == 1) checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('common.Active')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option" class="primary_checkbox d-flex mr-12">
                                            <input name="is_active" value="0" class="de_active" type="radio" @if ($leadger->is_active == 0) checked @endif>
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{trans('common.Inactive')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="edit_status_error"></span>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.add_as_sub_account') }} </label>
                                <label data-id="color_option"
                                       class="primary_checkbox d-flex mr-12">
                                    <input name="as_sub_category" value="1" class="as_sub_category_edit" type="checkbox" @if ($leadger->parent_id > 0) checked @endif>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" id="selected_parent_id" @if ($leadger->parent_id == 0) value="0" @else value="{{ $leadger->parent_id }}" @endif>
                        <div class="col-xl-12 parent_chartAccountEdit" @if ($leadger->parent_id == 0) style="display: none;" @endif>
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{ trans('account.select_parent_account') }} </label>
                                <div id="parent_chart_account_list_edit"></div>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center pt_20">
                                <button type="submit" class="primary-btn semi_large2 submit_button_form fix-gr-bg update_btn" id="save_button_parent"><i class="ti-check"></i>{{ trans('account.update') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
