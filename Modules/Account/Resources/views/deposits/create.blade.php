@extends('backEnd.master')
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{ __('common.Add New') }} {{ __('account.Deposit') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <form action="{{ route('account_deposit.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="chart_account_id">{{ __('account.Account') }} <span>*</span></label>
                                        <select class="primary_select mb-15" name="chart_account_id" id="chart_account_id" required>
                                            <option value="">{{ __('common.Select One') }}</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" {{ (string) $chartAccountId === (string) $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('chart_account_id') }}</span>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="date">{{ __('common.Date') }} <span>*</span></label>
                                        <input class="primary_input_field primary-input date form-control" id="startDate" type="text" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" autocomplete="off" required>
                                        <span class="text-danger">{{ $errors->first('date') }}</span>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="amount">{{ __('account.Amount') }} <span>*</span></label>
                                        <input class="primary_input_field primary-input form-control" id="amount" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" autocomplete="off" required>
                                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for="source">{{ __('account.Source / Reason') }} <span>*</span></label>
                                        <input class="primary_input_field primary-input form-control" id="source" type="text" name="source" value="{{ old('source') }}" placeholder="{{ __('account.e.g. Owner top-up, bank loan, emergency transfer') }}" autocomplete="off" required>
                                        <span class="text-danger">{{ $errors->first('source') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mt-4">
                                    <div class="submit_btn text-center">
                                        <button class="primary-btn semi_large2 fix-gr-bg" id="save"><i class="ti-check"></i>{{ __('common.Save') }}</button>
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
