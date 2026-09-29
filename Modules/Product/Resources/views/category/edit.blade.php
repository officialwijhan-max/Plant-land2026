
<div class="modal fade admin-query" id="Item_Edit">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('common.Edit Category') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <form action="" id="categoryEditForm">
                    <div class="row">
                        <input type="text" style="display: none;" class="edit_id" value="{{ $item->id }}">
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ __('common.Name') }} *</label>
                                <input name="name" class="primary_input_field name"
                                       placeholder="{{ __('product.Category Name') }}"
                                       type="text" required value="{{ $item->name }}">
                                <span class="text-danger" id="edit_name_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="code">{{ __('common.Code') }} </label>
                                <input name="code" class="primary_input_field code"
                                       placeholder="{{ __('product.Category Code') }}" id="code" value="{{ $item->code }}"
                                       type="text">
                                <span class="text-danger" id="edit_code_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="">{{ __('common.Description') }}</label>
                                <input name="description" class="primary_input_field description" value="{{ $item->description }}"
                                       placeholder="{{__('product.Put Some Description')}}" type="text">
                                <span class="text-danger" id="edit_description_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input">
                                <label class="primary_input_label"
                                       for="">{{ __('common.Status') }}</label>
                                <ul id="theme_nav" class="permission_list sms_list ">
                                    <li>
                                        <label data-id="bg_option"
                                               class="primary_checkbox d-flex mr-12">
                                            <input name="status" value="1" class="active" type="radio" @checked($item->status == 1)>
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{__('common.Active')}}</p>
                                    </li>
                                    <li>
                                        <label data-id="color_option"
                                               class="primary_checkbox d-flex mr-12">
                                            <input name="status" value="0" class="de_active" @checked($item->status == 0)
                                                   type="radio">
                                            <span class="checkmark"></span>
                                        </label>
                                        <p>{{__('common.DeActive')}}</p>
                                    </li>
                                </ul>
                                <span class="text-danger" id="edit_status_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label"
                                       for="">{{ __('common.Add as Sub Category') }}</label>
                                <label data-id="color_option"
                                       class="primary_checkbox d-flex mr-12">
                                    <input name="as_sub_category" value="1" class="as_sub_category_edit"
                                           type="checkbox" @checked($item->parent_id != 0)>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-xl-12 edit_parent_category" @if ($item->parent_id == 0) style="display: none;" @endif>
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{ __('common.Select parent Category') }} </label>
                                <select name="parent_id" id="parent_id" class="select2 mb-15 single_select primary_singleSelect mb-15 sub_cat_select category">
                                    @if ($item->parent_id != 0)
                                    <option value="{{ $item->parent_id }}">{{$item->parentCat->name}}</option>
                                    @else
                                    <option value="0">{{__('product.Select Category')}}</option>
                                    @endif
                                </select>
                            </div>
                        </div>


                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center pt_20">
                                <button type="submit" class="primary-btn semi_large2  fix-gr-bg"
                                        id="update_save_button_parent"><i
                                        class="ti-check"></i>{{ __('common.Update') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>