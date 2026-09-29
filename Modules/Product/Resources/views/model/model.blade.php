@extends('backEnd.master')
@section('mainContent')
    @include("backEnd.partials.alertMessage")
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('common.Model') }}</h3>
                                {{-- <ul class="d-flex">
                                    @if(permissionCheck('model.store'))
                                    <li><a data-toggle="modal" data-target="#Item_Details"
                                           class="primary-btn radius_30px mr-10 fix-gr-bg" href="#"><i
                                                class="ti-plus"></i>{{ __('common.Add Model') }}</a></li>
                                        @endif
                                        @if(permissionCheck('model.csv_upload.store'))
                                    <li><a class="primary-btn radius_30px mr-10 fix-gr-bg"
                                           href="{{route('model.csv_upload.create')}}"><i
                                                class="ti-export"></i>{{__('common.Upload Via CSV')}}</a></li>
                                        @endif
                                </ul> --}}
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div id="item_list_tbl">
                                @include('product::model.model_list')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Modal Item_Details -->

                <div class="modal fade admin-query" id="Item_Details">
                    <div class="modal-dialog modal_800px modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">{{ __('common.Add Model') }}</h4>
                                <button type="button" class="close " data-dismiss="modal">
                                    <i class="ti-close "></i>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form method="POST" id="modelTypeForm">
                                    <div class="row">

                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label" for="">{{ __('common.Name') }}
                                                    *</label>
                                                <input name="name" class="primary_input_field" placeholder="{{ __('product.Model Name') }}"
                                                       type="text" required>
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
                                                <label class="primary_input_label" for="">{{ __('common.Status') }}
                                                    *</label>
                                                <ul id="theme_nav" class="permission_list sms_list ">
                                                    <li>
                                                        <label data-id="bg_option"
                                                               class="primary_checkbox d-flex mr-12">
                                                            <input name="status" value="1" class="active" type="radio"
                                                                   checked>
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <p>{{__('common.Active')}}</p>
                                                    </li>
                                                    <li>
                                                        <label data-id="color_option"
                                                               class="primary_checkbox d-flex mr-12">
                                                            <input name="status" value="0" class="de_active"
                                                                   type="radio">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <p>{{__('common.DeActive')}}</p>
                                                    </li>
                                                </ul>
                                                <span class="text-danger" id="status_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 text-center">
                                            <div class="d-flex justify-content-center pt_20">
                                                <button type="submit" class="primary-btn semi_large2  fix-gr-bg"
                                                        id="save_button_parent"><i
                                                        class="ti-check"></i>{{ __('common.Add Model') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal fade admin-query" id="Item_Edit">
                    <div class="modal-dialog modal_800px modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">{{ __('common.Edit Model') }}</h4>
                                <button type="button" class="close " data-dismiss="modal">
                                    <i class="ti-close "></i>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="modelTypeEditForm">
                                    <div class="row">
                                        <input type="text" style="display: none;" class="edit_id">
                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label" for="">{{ __('common.Name') }}
                                                    *</label>
                                                <input name="name" class="primary_input_field name"
                                                       placeholder="{{ __('product.Model Name') }}"
                                                       type="text" required>
                                                <span class="text-danger" id="edit_name_error"></span>
                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="primary_input mb-25">
                                                <label class="primary_input_label"
                                                       for="">{{ __('common.Description') }}</label>
                                                <input name="description" class="primary_input_field description"
                                                       placeholder="{{__('product.Put Some Description')}}" type="text">
                                                <span class="text-danger" id="edit_description_error"></span>
                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">{{ __('common.Status') }}
                                                    *</label>
                                                <ul id="theme_nav" class="permission_list sms_list ">
                                                    <li>
                                                        <label data-id="bg_option"
                                                               class="primary_checkbox d-flex mr-12">
                                                            <input name="status" value="1" class="active" type="radio">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <p>{{__('common.Active')}}</p>
                                                    </li>
                                                    <li>
                                                        <label data-id="color_option"
                                                               class="primary_checkbox d-flex mr-12">
                                                            <input name="status" value="0" class="de_active"
                                                                   type="radio">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <p>{{__('common.DeActive')}}</p>
                                                    </li>
                                                </ul>
                                                <span class="text-danger" id="edit_status_error"></span>
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

            </div>
        </div>

        <div class="showModalHideColumn"></div>
        @php
            $employee_per = auth()->user()->user_col_permissions->where('table_name', 'model_list')->first();
        @endphp
        @if ($employee_per)
            @if ($employee_per->hide_column_no_by_self)
                <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="{{ $employee_per->hide_column_no_by_self }}">
            @endif
        @else
            <input type="hidden" name="hidden_list_tbl" id="hidden_list_tbl" value="">
        @endif
        <input type="hidden" name="th_name" id="th_name" value="[['1','{{ __('common.ID') }}','id'],['2','{{ __('common.Name') }}','name'],['3','{{ __('common.Description') }}','description'],['4','{{ __('common.Status') }}','status'],['5','{{ __('common.Action') }}']]">
        <input type="hidden" name="hide_show_permission_by_self" id="hide_show_permission_by_self" value="{{ route('user_column_permission.show_with_self',['model_list']) }}">

        @include('backEnd.partials.delete_modal')
    </section>
@endsection
@push('scripts')
    <script src="{{ Module::asset('tables:table.js') }}"></script>
    <script src="{{ Module::asset('tables:hide_show.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            "use strict";
            var baseUrl = $('#app_base_url').val();
            $(document).ready(function () {

                $("#modelTypeForm").on("submit", function (event) {
                    event.preventDefault();
                    var row_count = $('.row_filter_option').find(":selected").val();
                    let formData = $(this).serializeArray();
                    $.each(formData, function (key, message) {
                        $("#" + formData[key].name + "_error").html("");
                    });
                    $.ajax({
                        url: "{{route("model.store")}}",
                        data: formData,
                        type: "POST",
                        success: function (response) {
                            $("#Item_Details").modal("hide");
                            $("#modelTypeForm").trigger("reset");
                            getData(1, row_count);
                            toastr.success(response.message, trans('js.Success'));
                        },
                        error: function (error) {
                            if (error) {
                                $.each(error.responseJSON.errors, function (key, message) {
                                    $("#" + key + "_error").html(message[0]);
                                });
                            }
                        }

                    });
                });


                $("#item_list_tbl").on("click", ".edit_model_type", function (event) {
                    let id = $(this).data("value");
                    $.ajax({
                        url: "{{url('/')}}" + "/product/model/" + id + "/edit",
                        type: "GET",
                        success: function (response) {
                            $(".edit_id").val(response.id);
                            $(".name").val(response.name);
                            $(".description").val(response.description);
                            if (response.status === 1) {
                                $(".active").attr("checked", true);
                            } else {
                                $(".de_active").attr("checked", true);
                            }
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                });


                $(document).on("submit", "#modelTypeEditForm", function (event) {
                    event.preventDefault();
                    let id = $(".edit_id").val();
                    var row_count = $('.row_filter_option').find(":selected").val();
                    let formData = $(this).serializeArray();
                    $.each(formData, function (key, message) {
                        $("#edit_" + formData[key].name + "_error").html("");
                    });
                    $.ajax({
                        url: "{{url('/')}}" + "/product/model/" + id,
                        data: formData,
                        type: "PUT",
                        dataType: "JSON",
                        success: function (response) {
                            $("#Item_Edit").modal("hide");
                            $("#modelTypeEditForm").trigger("reset");
                            $(".active").attr("checked", false);
                            $(".de_active").attr("checked", false);
                            getData(1, row_count);
                            toastr.success(response.message, trans('js.Success'));
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
        });

    </script>
@endpush
