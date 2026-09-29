@extends('backEnd.master')
@section('page-title', Settings("site_title") .' | '. trans('account.import_trial_balance_for_the_first_time_only'))
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{ trans('account.import_trial_balance_for_the_first_time_only') }}</h3>
                            </div>
                        </div>
                        <form action="{{ route('vouchers.trial_balance.import_store') }}" method="POST" enctype="multipart/form-data" class="csvForm">
                            @csrf
                            <div class="row form">
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                   <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="">{{ trans('account.csv_upload') }}</label>
                                        <div class="primary_file_uploader">
                                            <input class="primary-input" type="text" id="placeholderFileOneName" placeholder="{{__("base::base.browse_file")}}" readonly="">
                                            <button class="" type="button">
                                                <label class="primary-btn small fix-gr-bg" for="document_file_1">{{__("base::base.browse")}} </label>
                                                <input type="file" class="d-none" accept=".xlsx, .xls, .csv" name="file" id="document_file_1">
                                            </button>
                                        </div>
                                        <span class="text-danger">{{$errors->first('file')}}</span>
                                   </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                   <div class="primary_input mb-15">
                                      <label class="primary_input_label red_input" for="">{{ trans("base::base.please_download_the_sample_file_input_your_desire_information_then_upload._Don't_try_to_upload_different_file_format_and_information") }}</label>
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
@endsection

@push("scripts")
   <script src="{{ Module::asset('common:upload_browse.js') }}"></script>
@endpush
