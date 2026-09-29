@extends('backEnd.master')
@section('mainContent')

    <div id="add_payment">
        <section class="admin-visitor-area up_st_admin_visitor">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{ __('common.Add New') }} Payment</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="white_box_50px box_shadow_white">
                            <!-- Prefix  -->
                            <form action="{{ route('staff.payment_request.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">Staff *</label>
                                            <select class="select2 mb-15 single_select primary_singleSelect" name="staff_id" required>
                                                <option>{{__('common.Select One')}}</option>
                                                @foreach ($staff as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{$errors->first('staff_id')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Payment From Account')}} *</label>
                                            <select class="select2 mb-15 single_select primary_singleSelect" name="bank_id" required>
                                                <option>{{__('common.Select One')}}</option>
                                                @foreach ($bank_accounts as $item)
                                                    <option value="{{ $item->id }}">{{ $item->bank_name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{$errors->first('bank_id')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> {{__("account.Narration")}} </label>
                                            <input class="primary_input_field" name="narration"
                                                   placeholder="{{__("account.Narration")}}" type="text"
                                                   value="{{ old('narration') }}">
                                            <span class="text-danger">{{$errors->first('narration')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> Region </label>
                                            <input class="primary_input_field" name="region"
                                                   placeholder="Region" type="text"
                                                   value="{{ old('region') }}">
                                            <span class="text-danger">{{$errors->first('region')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> Amount </label>
                                            <input class="primary_input_field" name="amount"
                                                   placeholder="Amount" type="number" required
                                                   value="{{ old('amount') }}">
                                            <span class="text-danger">{{$errors->first('amount')}}</span>
                                        </div>
                                    </div>
                                    
                                </div>
                               
                                <div class="row">
                                    <div class="col-12">
                                        <div class="submit_btn text-center ">
                                            <button class="primary-btn semi_large2 fix-gr-bg"><i
                                                    class="ti-check"></i>{{__("common.Save")}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
@endsection

@push("scripts")
<script type="text/javascript">
$(".primary_singleSelect").select2();
   
</script>
    
@endpush
