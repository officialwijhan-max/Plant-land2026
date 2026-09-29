<div class="modal fade admin-query" id="create_contact">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('contact.Customer') }} / {{ trans('contact.Supplier') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="create_contact_form">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{trans('contact.Contact Type')}} *</label>
                                <select class="primary_select mb-15 contact_type" name="contact_type">
                                    <option selected disabled>{{trans('account.select_one')}}</option>
                                    <option value="Supplier">{{trans('contact.Supplier')}}</option>
                                    <option value="Customer">{{trans('contact.Customer')}}</option>
                                </select>
                                <span class="text-danger">{{$errors->first('contact_type')}}</span>
                            </div>
                        </div>
                        <input type="hidden" name="row_counts" id="row_counts" value="1">
                        <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> {{__("common.Name")}} *</label>
                                <input class="primary_input_field contact_name" name="name" placeholder="{{__('common.Name')}}" type="text" value="{{old('name')}}" required>
                                <span class="text-danger">{{$errors->first('name')}}</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{trans('common.Mobile')}}</label>
                                <input type="text" name="mobile" class="primary_input_field" value="{{old('mobile')}}">
                                <span class="text-danger">{{$errors->first('mobile')}}</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{trans('common.Email')}}</label>
                                <input type="text" name="email" class="primary_input_field" value="{{old('email')}}">
                                <span class="text-danger">{{$errors->first('email')}}</span>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for=""> {{trans('common.Address')}}</label>
                                <input class="primary_input_field" name="address" placeholder="{{trans('common.Address')}}" type="text" value="{{old('address')}}">
                                <span class="text-danger">{{$errors->first('address')}}</span>
                            </div>
                        </div>

                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center">
                                <button class="primary-btn semi_large2 fix-gr-bg mr-10"  type="submit"><i class="ti-check"></i>{{trans('account.save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
