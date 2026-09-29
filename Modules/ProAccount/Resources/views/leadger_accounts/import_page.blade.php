@extends('backEnd.master')
@section('page-title', Settings("site_title") .' | '. trans('account.import_leadger_accounts'))
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{ trans('account.import_leadger_accounts') }}</h3>
                            </div>
                        </div>
                        <form action="{{ route('leadger.import_store') }}" method="POST" enctype="multipart/form-data" class="csvForm">
                            @csrf
                            <div class="row form">
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                   <div class="primary_input mb-15">
                                      <label class="primary_input_label" for="">{{ trans("common.CSV File Upload"); }} <small><a class="d-flex float-right" href="{{ asset('bulk_upload_sample/leadger_csv.xlsx') }}" download>{{ trans('common.Sample File Download') }}</a><small> </label>
                                      <div class="primary_file_uploader">
                                         <input class="primary-input" type="text" id="placeholderFileOneName" placeholder="{{__("account::account.browse")}}" readonly="">
                                         <button class="" type="button">
                                         <label class="primary-btn small fix-gr-bg" for="document_file_1">{{__("account::account.browse")}} </label>
                                         <input type="file" class="d-none" accept=".xlsx, .xls, .csv" name="file" id="document_file_1">
                                         </button>
                                      </div>
                                      <span class="text-danger">{{$errors->first('file')}}</span>
                                   </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                   <div class="primary_input mb-15">
                                      <label class="primary_input_label red_input" for="">{{ trans("account.Please Download the sample file input your desire information then upload. Don't try to upload different file format and information") }}</label>
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
