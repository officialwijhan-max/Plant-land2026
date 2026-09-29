<!DOCTYPE html>
<html>
<head>

    <title>{{ trans('account.cashbook) }}</title>

    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/rtl/bootstrap.min.css"/>

    <style>
        @font-face {
            font-family: 'Cerebri Sans',
            url('storage/fonts/Poppins-Regular.ttf');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cerebri Sans',
            url('storage/fonts/Poppins-Medium.ttf');
            font-weight: 500;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cerebri Sans',
            url('storage/fonts/Poppins-SemiBold.ttf');
            font-weight: 600;
            font-style: normal;
        }
        .invoice_heading {
            border-bottom: 1px solid black;
            padding: 20px;
            text-transform: capitalize;
        }
        body{
            font-family: "Poppins", sans-serif;
        }
        .invoice_logo {
            width: 33.33%;
            float: left;
            text-align: left;
        }

        .invoice_no {
            text-align: right;
            color: #415094;
        }

        .invoice_info {
            padding: 20px;
            width: 100%;
            text-transform: capitalize;
        }
        .t-100{
            min-height: 100px;
        }

        .billing_info {
            margin-top: 20px;
        }

        table {
            text-align: left;
            font-family: "Poppins", sans-serif;
        }

        td, th {
            color: #000000 !important;
            font-size: 10px;
            padding: 0;
            font-weight: 400;
            font-family: "Poppins", sans-serif;
        }

        th {
            font-weight: 600;
            font-family: "Poppins", sans-serif;
        }

        li {
            list-style-type: none;
            text-align: right;
        }

        .table-bordered {
            border: 1px solid #000000 !important;
        }

        .sale_note {
            width: 45%;
            float: left;
            text-align: left;
        }

        .notes {
            color: #415094;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .note_details {
            font-size: 12px;
            font-weight: 600;
            color: #828BB2 !important;
        }
        .margin_120{
            margin-top: 120px;
            font-size: 12px;
        }.margin_12{
            margin-bottom: 120px;
            font-size: 12px;
        }
        .invoice_footer{
            position: absolute;
            left: 0;
            bottom: 180px;
            width: 100%;
        }

        .invoice_info_footer {
            padding: 0px;
            width: 100%;
            left: 0;
            text-transform: capitalize;
            position: inherit;
        }

        p {
            font-size: 10px;
            color: #000000;
            line-height: 16px;
        }
        .extra_div {
            height:40px;
        }
        .a4_width {
           max-width: 1145.28px;
           margin: auto;
        }
        .nowrap{
            white-space: nowrap;
        }

        h5 {
            font-size: 13px !important;
            font-weight: 500;
            line-height: 12px;
        }
        .hpb-1{
            padding: 0;
        }
        .width_custom{
            max-width: 200px;
        }
    </style>
</head>
<body>
@php
$setting = app('general_setting');
$balanceDebit = 0;
$balanceCredit = 0;
$till_now_balance = $total_transactions->where('type', 'Cr')->sum('amount') - $total_transactions->where('type', 'Dr')->sum('amount');
$today_balance_in_hand = $balanceCredit - $balanceDebit;
@endphp
<div class="container-fluid ">
    <div class="invoice_heading">
        <div class="invoice_logo">
            <img src="{{asset(app('general_setting')->logo)}}" style="max-height: 220px; max width: 500px" alt="">
        </div>
        <div class="invoice_no">
            <h5 class="hpb-1">{{app('general_setting')->company_name}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->phone}}</h5>
            <h5 class="hpb-1">{{app('general_setting')->email}}</h5>
            <h5>{{app('general_setting')->address}}</h5>
            <h5>{{trans("common.Print")}} : {{date('m-d-Y')}}</h5>
        </div>
    </div>
    <div class="invoice_info">
        <table class="table table-bordered billing_info m-0">
            <tbody>
                <tr>
                    <td>
                        <table class="table table-bordered billing_info m-0">
                            <thead>
                                <tr>
                                    <th colspan="2">
                                        <h6 class="text-center">{{ trans('account.debit/expense') }} : @isset($start_date) {{ date('m/d/Y', strtotime($start_date)) .' - '. date('m/d/Y', strtotime($end_date)) }} @else {{date('m/d/Y')}} @endisset</h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col">{{ trans('account.account_name') }}</th>
                                    <th scope="col" class="text-right">{{ trans('account.amount') }}</th>
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
                                                <th>{{$debit->leadger->name}}</th>
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
                    </td>
                    <td>
                        <table class="table table-bordered billing_info m-0">
                            <thead>
                                <tr>
                                    <th colspan="2">
                                        <h6 class="text-center">{{ trans('account.credit/income') }} : @isset($start_date) {{ date('m/d/Y', strtotime($start_date)) .' - '. date('m/d/Y', strtotime($end_date)) }} @else {{date('m/d/Y')}} @endisset</h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col">{{ trans('account.account_name') }}</th>
                                    <th scope="col" class="text-right">{{ trans('account.amount') }}</th>
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
                                                <th>{{$credit->leadger->name}}</th>
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
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table class="table table-bordered billing_info m-0">
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
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script src="{{asset('public/backEnd/vendors/js/jquery-3.6.0.min.js')}}"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        window.print();
    });
</script>
</body>
</html>
