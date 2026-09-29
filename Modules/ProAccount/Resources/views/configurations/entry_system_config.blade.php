
@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.accounting_configuration'))
@section('mainContent')
    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{ trans('Pro-Accounting Module') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <form action="{{ route('account.configuration_update_for_approval') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="box_header">
                                        <div class="main-title d-flex">
                                            <h3 class="mb-0 mr-30">{{ trans('Activate Pro-Accounting Module') }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <ul class="permission_list sms_list">
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="current_active_accounting" class="current_active_accounting" type="radio" id="current_active_accounting_1" value="pro" @if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "pro") checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ trans('common.Yes') }}</p>
                                        </li>
                                        <li>
                                            <label class="primary_checkbox d-flex mr-12 ">
                                                <input name="current_active_accounting" class="current_active_accounting" type="radio" id="current_active_accounting_2" value="normal" @if (moduleStatusCheck('ProAccount') && Settings('current_active_accounting') == "normal") checked @endif>
                                                <span class="checkmark"></span>
                                            </label>
                                            <p>{{ trans('common.No') }}</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="submit_btn text-center ">
                                        <button class="primary-btn semi_large2 submit_button_form fix-gr-bg"><i class="ti-check"></i>{{trans("account.save")}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
