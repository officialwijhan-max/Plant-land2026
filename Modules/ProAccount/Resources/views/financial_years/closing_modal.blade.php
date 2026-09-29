<div class="modal fade admin-query" id="close_details_modal">
    <div class="modal-dialog modal_800px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('account.financial_year_closing') }}</h4>
                <button type="button" class="close " data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('financial_years.close_now', $row->id) }}" method="post" id="chart_account_rename_form">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="primary_input mb-25">
                                <label class="primary_input_label" for="">{{ trans('account.start_date') }}</label>
                                <input name="start_date" class="primary_input_field start_date" placeholder="{{ trans('account.start_date') }}" type="text" value="{{ date('m/d/Y', strtotime($row->start_date)) }}" required readonly>
                                <span class="text-danger" id="start_date_error"></span>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="primary_input mb-15">
                                <label class="primary_input_label" for="">{{trans('account.closing_date')}} *</label>
                                <div class="primary_datepicker_input">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="">
                                                <input placeholder="{{ trans('account.end_date') }}" class="primary_input_field primary-input date form-control" id="closingDate" type="text" name="end_date" value="{{date('m/d/Y')}}" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="start-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <div class="d-flex justify-content-center pt_20">
                                <button type="submit" class="primary-btn semi_large2 fix-gr-bg update_btn" id="save_button_parent"><i class="ti-check"></i>{{ trans('account.update') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
