@extends('backEnd.master')
@section('page-title', Settings("site_title") .' | '. trans('account.upload_banking_transaction'))
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{ trans('account.upload_banking_transaction') }}</h3>
                            </div>
                        </div>
                        <form action="{{ route('banking_statement.store') }}" method="POST" enctype="multipart/form-data" class="csvForm">
                            @csrf
                            <div class="row form">
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ __('account.Account') }} *</label>
                                        <div class="sub_account_below_div">
                                            <select class="select2 mb-15 credit_account_id" name="credit_account_id" id="credit_account_id">
                                                <option value="0">{{trans('account.Select one')}}</option>
                                            </select>
                                        </div>
                                        <span class="text-danger">{{$errors->first('')}}</span>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                   <div class="primary_input mb-15">
                                      <label class="primary_input_label" for="">{{ trans('account.csv_upload') }} <small><a class="d-flex float-right" href="{{ asset('public/bulk_upload_sample/banking_transaction_demo.xlsx') }}" download>{{ trans('common.Sample File Download') }}</a><small> </label>
                                      <div class="primary_file_uploader">
                                         <input class="primary-input" type="text" id="placeholderFileOneName" placeholder="{{__("proaccount::account.browse")}}" readonly="">
                                         <button class="" type="button">
                                         <label class="primary-btn small fix-gr-bg" for="document_file_1">{{__("proaccount::account.browse")}} </label>
                                         <input type="file" class="d-none" accept=".xlsx, .xls, .csv" name="file" id="document_file_1">
                                         </button>
                                      </div>
                                      <span class="text-danger">{{$errors->first('file')}}</span>
                                   </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                   <div class="primary_input mb-15">
                                      <label class="primary_input_label red_input" for="">{{ trans("account.please_download_the_sample_file_input_your_desire_information_then_upload._Don't_try_to_upload_different_file_format_and_information") }}</label>
                                   </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="submit_btn text-center ">
                                    <button class="primary-btn semi_large2 fix-gr-bg csvFormBtn" type="submit"><i class="ti-check"></i>{{trans('account.upload_now')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('proaccount::leadger_accounts.create')
    <input type="hidden" value="{{route('voucher.get_accounts_for_select_option')}}" id="get_accounts_for_payment">
    <input type="hidden" value="{{route('leadger.get_leadger_for_select')}}" id="leadger_list_select_option">
    <input type="hidden" value="{{route('leadger.store')}}" id="chart_account_form_url">
    <input type="hidden" value="{{route('leadger.cost_center')}}" id="cost_center_url">
@endsection

@push("scripts")
   <script src="{{ Module::asset('common:upload_browse.js') }}"></script>
   <script src="{{ Module::asset('account:income_entry.js') }}"></script>
   <script src="{{ Module::asset('account:instant_create.js') }}"></script>
@endpush