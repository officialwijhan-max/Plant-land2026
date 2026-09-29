@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('common.Category') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <div id="item_list_tbl">
                                @include('product::category.paginate_list')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Modal Item_Details -->

                <div class="modal fade admin-query" id="Item_Details">
                    <div class="modal-dialog modal_800px modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">{{ __('common.Add Category') }}</h4>
                                <button type="button" class="close " data-dismiss="modal">
                                    <i class="ti-close "></i>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form method="POST" id="categoryForm" action="{{ route("category.store") }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{ __('common.Name') }} *</label>
                                                <input name="name" class="primary_input_field"
                                                       placeholder="{{ __('product.Category Name') }}"
                                                       type="text" required>
                                                <span class="text-danger" id="name_error"></span>
                                            </div>
                                        </div>


                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{ __('common.Code') }} </label>
                                                <input name="code" class="primary_input_field"
                                                       placeholder="{{ __('product.Category Code') }}"
                                                       type="text">
                                                <span class="text-danger" id="name_error"></span>
                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{ __('common.Description') }}</label>
                                                <input name="description" class="primary_input_field"
                                                       placeholder="{{__('product.Put Some Description')}}" type="text">
                                                <span class="text-danger" id="description_error"></span>
                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label"
                                                       for="">{{ __('common.Status') }} *</label>
                                                <ul id="theme_nav" class="permission_list sms_list ">
                                                    <li>
                                                        <label data-id="bg_option"
                                                               class="primary_checkbox d-flex mr-12 ">
                                                            <input name="status" value="1" type="radio" checked>
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <p>{{__('common.Active')}}</p>
                                                    </li>
                                                    <li>
                                                        <label data-id="color_option"
                                                               class="primary_checkbox d-flex mr-12">
                                                            <input name="status" value="0" type="radio">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <p>{{__('common.DeActive')}}</p>
                                                    </li>
                                                </ul>
                                                <span class="text-danger" id="status_error"></span>
                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{ __('common.Add as Sub Category') }} </label>
                                                <label data-id="color_option"
                                                       class="primary_checkbox d-flex mr-12">
                                                    <input name="as_sub_category" value="1"
                                                           class="as_sub_category"
                                                           type="checkbox">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-xl-12 parent_category" style="display: none;">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{ __('common.Select parent Category') }} </label>
                                                <select name="parent_id" id="parent_id" class="select2 mb-15 single_select primary_singleSelect mb-15 sub_cat_select category">
                                                    <option value="0">{{__('product.Select Category')}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 text-center">
                                            <div class="d-flex justify-content-center pt_20">
                                                <button type="submit" class="primary-btn semi_large2  fix-gr-bg"
                                                        id="save_button_parent"><i
                                                        class="ti-check"></i>{{ __('common.Add Category') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="edit_modal"></div>
            </div>
        </div>
        @include('backEnd.partials.delete_modal')
        <input type="hidden" name="category_list_select_option" id="category_list_select_option" value="{{ route('category.list_select_option') }}">
        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'category_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{__('common.Name')}}','name'],['2','{{ __('common.Code') }}','code'],['3','{{ __('common.Parent') }}','parent'],['4','{{ __('common.Description') }}','description'],['5','{{ __('common.Status') }}','status'],['6','{{ __('common.Action') }}','action']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['category_list']) }}">

    </section>
@endsection

@push('scripts')
<script src="{{ Module::asset('tables:table.js') }}"></script>
<script src="{{ Module::asset('tables:hide_show.js') }}"></script>
<script src="{{ Module::asset('core:category_select.js') }}"></script>
<script type="text/javascript">

    (function ($){
        "use strict";
        var baseUrl = $('#app_base_url').val();
        $(document).ready(function () {
            $('select').niceSelect();

            $(".as_sub_category").unbind().on("click", function () {
                $(".parent_category").toggle();
            });


            $(".as_sub_category_edit").unbind().on("click", function () {
                $(".edit_parent_category").toggle();
            });


            // $("#categoryForm").on("submit", function (event) {
            //     event.preventDefault();
            //     let formData = $(this).serializeArray();
            //     $.each(formData, function (key, message) {
            //         $("#" + formData[key].name + "_error").html("");
            //     });
            //     $.ajax({
            //         url: "{{route("category.store")}}",
            //         data: formData,
            //         type: "POST",
            //         success: function (response) {
            //             $("#Item_Details").modal("hide");
            //             $("#categoryForm").trigger("reset");
            //             $(".parent_category").hide();
            //             categoryList();
            //             $('select').niceSelect('update');
            //             toastr.success(response.message, trans('js.Success'));
            //         },
            //         error: function (error) {
            //             if (error) {
            //                 $.each(error.responseJSON.errors, function (key, message) {
            //                     $("#" + key + "_error").html(message[0]);
            //                 });
            //             }
            //         }

            //     });
            // });


            $("#item_list_tbl").on("click", ".edit_category", function () {
                let id = $(this).data("value");
                $(".as_sub_category_edit").attr("checked", false);
                $(".edit_parent_category").hide();
                $.ajax({
                    url: "{{url('/')}}" + "/product/category/" + id + "/edit",
                    type: "GET",
                    success: function (response) {
                        $('.edit_modal').html(response);
                        $('#Item_Edit').modal('show');
                        getCategoryList();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });

            $(document).on("submit", "#categoryEditForm", function (event) {
                event.preventDefault();
                let id = $(".edit_id").val();
                var row_count = $('.row_filter_option').find(":selected").val();
                let formData = $(this).serializeArray();
                $.each(formData, function (key, message) {
                    $("#edit_" + formData[key].name + "_error").html("");
                });
                $.ajax({
                    url: "{{url('/')}}" + "/product/category/" + id,
                    data: formData,
                    type: "PUT",
                    dataType: "JSON",
                    success: function (response) {
                        toastr.success(response.message, trans('js.Success'));
                        $("#Item_Edit").modal("hide");
                        $("#categoryEditForm").trigger("reset");
                        $(".active").attr("checked", false);
                        $(".de_active").attr("checked", false);
                        getData(1, row_count);
                        // categoryList();
                    },
                    error: function (error) {
                        if (error) {
                            $.each(error.responseJSON.errors, function (key, message) {
                                $("#edit_" + key + "_error").html(message[0]);
                            });
                        }
                    }
                });
            });
        });
    })(jQuery);
</script>
@endpush