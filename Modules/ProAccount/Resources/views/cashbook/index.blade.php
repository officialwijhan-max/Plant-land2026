@extends('backEnd.master')
@section('page-title', Settings('site_title') .' | '. trans('account.cashbook'))
@section('mainContent')
    @php
        $balanceDebit = 0;
        $balanceCredit = 0;
    @endphp
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="col-12">
                <div class="box_header">
                    <div class="main-title d-flex">
                        <h3 class="mb-0 mr-30">{{__("proaccount::account.cashbook_of_branch_account")." : ". $branchAccount->name}}</h3>
                    </div>
                </div>
            </div>
            <div class="white_box_50px p-5 mb-20">
                <div class="row justify-content-center">
                    <div class="col-md-6 mb-3">
                        <div class="white_box_50px box_shadow_white p-5">
                            <form class="" action="index.html" method="post">
                                <div class="primary_input mb-15">
                                    <label class="primary_input_label" for=""> {{__("proaccount::account.openning_balance")}} </label>
                                    <input class="primary_input_field" name="narration" type="text" value="{{single_price($total_transactions->where('type', 'Cr')->sum('amount') - $total_transactions->where('type', 'Dr')->sum('amount'))}}" readonly>
                                    <span class="text-danger">{{$errors->first('narration')}}</span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="white_box_50px box_shadow_white p-5">
                            <form class="" action="{{ route('cashbook.index') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{trans('account.date_from')}} </label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="position_relative">
                                                            @isset($start_date)
                                                                <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="start_date" type="text" name="start_date" value="{{ date('m/d/Y', strtotime($start_date)) }}" autocomplete="off" required>
                                                                <div class="custom_datepicker_design"></div>
                                                            @else
                                                                <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="start_date" type="text" name="start_date" value="{{ date('m/d/Y') }}" autocomplete="off" required>
                                                                <div class="custom_datepicker_design"></div>
                                                            @endisset
                                                        </div>
                                                    </div>
                                                    <button class="date-icon" type="button">
                                                        <i class="ti-calendar"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{trans('account.date_to')}} </label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="position_relative">
                                                            @isset($end_date)
                                                                <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="end_date" type="text" name="end_date" value="{{ date('m/d/Y', strtotime($end_date)) }}" autocomplete="off" required>
                                                                <div class="custom_datepicker_design"></div>
                                                            @else
                                                                <input placeholder="{{trans('account.date')}}" class="primary_input_field primary-input custom-date form-control" id="end_date" type="text" name="end_date" value="{{ date('m/d/Y') }}" autocomplete="off" required>
                                                                <div class="custom_datepicker_design"></div>
                                                            @endisset
                                                        </div>
                                                    </div>
                                                    <button class="date-icon" type="button">
                                                        <i class="ti-calendar"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="primary_btn_2 mt-30" type="submit" width="100%">{{ trans('account.search') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="white_box_50px p-5">
                <div class="row">
                    <div class="col-lg-12">
                        @if(strpos($_SERVER['REQUEST_URI'], '?') == true)
                            <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                               href="{{Illuminate\Support\Facades\Request::fullUrl()}}&print=1"><i
                                    class="ti-printer"></i>{{trans('account.print')}}</a>
                        @else
                            <a class="primary-btn radius_30px mr-10 fix-gr-bg" target="_blank"
                               href="{{Illuminate\Support\Facades\Request::fullUrl()}}?print=1"><i
                                    class="ti-printer"></i>{{trans('account.print')}}</a>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.debit/expense') }}</h3>
                            </div>
                        </div>
                        <div class="">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ trans('account.account_name') }}</th>
                                        <th class="text-right">{{ trans('account.amount') }}</th>
                                    </tr>
                                </thead>
                                @isset($debit_transactions)
                                    <tbody>
                                        @foreach($debit_transactions as $key => $debit)
                                            @php
                                                if (count($debit_transactions_balance) > 0) {
                                                    $balanceDebit += $debit_transactions_balance[$debit->account_id];
                                                }else {
                                                    $balanceDebit = 0;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{$debit->leadger->name}}</td>
                                                <td class="text-right">{{single_price($debit_transactions_balance[$debit->account_id])}}</td>
                                            </tr>
                                        @endforeach
                                        <tfoot>
                                            <td>{{ trans('account.total') }}</td>
                                            <td class="text-right">{{ single_price($balanceDebit) }}</td>
                                        </tfoot>
                                    </tbody>
                                @endisset
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="box_header common_table_header">
                            <div class="main-title d-md-flex">
                                <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ trans('account.credit/income') }}</h3>
                            </div>
                        </div>
                        <div class="">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>{{ trans('account.account_name') }}</th>
                                    <th class="text-right">{{ trans('account.amount') }}</th>
                                </tr>
                                </thead>
                                @isset($credit_transactions)
                                    <tbody>
                                        @foreach($credit_transactions as $key => $credit)
                                            @php
                                                if (count($credit_transactions_balance) > 0) {
                                                    $balanceCredit += $credit_transactions_balance[$credit->account_id];
                                                }else {
                                                    $balancerCedit = 0;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{$credit->leadger->name}}</td>
                                                <td class="text-right">{{single_price($credit_transactions_balance[$credit->account_id])}}</td>
                                            </tr>
                                        @endforeach
                                        <tfoot>
                                            <td>{{ trans('account.total') }}</td>
                                            <td class="text-right">{{ single_price($balanceCredit) }}</td>
                                        </tfoot>
                                    </tbody>
                                @endisset
                            </table>
                        </div>
                    </div>
                </div>
                @php
                    $till_now_balance = $total_transactions->where('type', 'Cr')->sum('amount') - $total_transactions->where('type', 'Dr')->sum('amount');
                    $today_balance_in_hand = $balanceCredit - $balanceDebit;
                @endphp
                <div class="row">
                    <div class="col-lg-12 mt-20">
                        <div class="">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>{{ trans('account.openning_balance') }}</td>
                                        <td class="text-right">{{ single_price($till_now_balance) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __("proaccount::account.Today's Total Income") }}</td>
                                        <td class="text-right">{{ single_price($balanceCredit) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __("proaccount::account.Today's Total Expense") }}</td>
                                        <td class="text-right">{{ single_price($balanceDebit) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __("proaccount::account.Today's Balance\Cash in Hand") }}</td>
                                        <td class="text-right">{{ single_price($today_balance_in_hand) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>{{ __("proaccount::account.Today's Closing Balance") }}</td>
                                        <td class="text-right">{{single_price($till_now_balance + $today_balance_in_hand)}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
