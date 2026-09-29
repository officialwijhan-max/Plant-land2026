<!DOCTYPE html>
<html>
<head>

    <title>{{ trans('account.partner_summary_reports') }} Print</title>

    <!-- Required meta tags -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/css/rtl/bootstrap.min.css"/>

    <style>
        .invoice_heading {
            border-bottom: 1px solid black;
            padding: 20px;
            text-transform: capitalize;
        }
        body{
            font-family: "Poppins", sans-serif;
        }
        .invoice_logo {
            width: 50%;
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
            min-height: 100px;
        }
        table {
            text-align: left;
            font-family: "Poppins", sans-serif;
        }

        td, th {
            color: #828bb2;
            font-size: 13px;
            font-weight: 400;
            font-family: "Poppins", sans-serif;
        }

        th {
            font-weight: 600;
            font-family: "Poppins", sans-serif;
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
            color: #454545;
            line-height: 16px;
        }
        .extra_div {
            height:100;
        }
        .a4_width {
           max-width: 210mm;
           margin: auto;
        }
        h5 {
            font-size: 13px !important;
            font-weight: 500;
            line-height: 12px;
        }
    </style>
</head>
<body>
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
    @php
        if ($leadgerAccount->morphable_type == "Modules\Hr\Entities\Employee") {
            $accountName = "EMPLOYEE ACCOUNT";
        }elseif ($leadgerAccount->morphable_type == "Modules\Customer\Entities\Customer") {
            $accountName = "CUSTOMER ACCOUNT";
        }elseif ($leadgerAccount->morphable_type == "Modules\Purchase\Entities\Supplier") {
            $accountName = "SUPPLIER ACCOUNT";
        }else {
            $accountName = "PARTNER ACCOUNT";
        }
    @endphp
    <div class="invoice_info">
        <h6 class="text-center">{{ $accountName }} : ({{ $leadgerAccount->code }}) {{$leadgerAccount->name}}</h6>
        <table class="table table-bordered billing_info m-0">
            <thead>
                <tr>
                    <th>{{ trans("account.account") }}</th>
                    <th>{{ trans("account.fiscal_year") }}</th>
                    <th>{{ trans("account.date") }}</th>
                    <th>{{ trans("account.type") }}</th>
                    <th width="30%">{{ trans("account.label") }}</th>
                    <th>{{ trans("account.debit") }}</th>
                    <th>{{ trans("account.credit") }}</th>
                    <th>{{ trans("account.balance") }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $group_account = [];
                    $total_debit_amount = 0;
                    $total_credit_amount = 0;
                @endphp
                @foreach ($transactions as $key => $transaction)
                    @if (!in_array($transaction->leadger_id, $group_account))
                        @php
                            array_push($group_account, $transaction->leadger_id);
                            $debit_total = 0;
                            $credit_total = 0;
                        @endphp
                        <tr>
                            <td class="head_color" colspan="8">{{ $transaction->leadger->code }} - {{ $transaction->leadger->name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td></td>
                        <td>{{  date(Settings("date_format_id"), strtotime($transaction->fiscal_year->start_date)) }}</td>
                        <td>{{  date(Settings("date_format_id"), strtotime($transaction->date)) }}</td>
                        <td>
                            @if ($transaction->voucher->type == "cash" || $transaction->voucher->type == "rec_cash" || $transaction->voucher->type == "pay_cash")
                                {{ trans('account.cash') }}
                            @elseif ($transaction->voucher->type == "bank" || $transaction->voucher->type == "rec_bank" || $transaction->voucher->type == "pay_bank")
                                {{ trans('account.bank') }}
                            @else
                                {{ trans('account.miscellaneous') }}
                            @endif
                        </td>
                        <td>{{ $transaction->voucher->narration }}</td>
                        <td class="text-right">
                            @if ($transaction->type == "Dr")
                                @php
                                    $debit_total += $transaction->amount;
                                    $total_debit_amount += $transaction->amount;
                                @endphp
                                {{ single_price($transaction->amount) }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($transaction->type == "Cr")
                                @php
                                    $credit_total += $transaction->amount;
                                    $total_credit_amount += $transaction->amount;
                                @endphp
                                {{ single_price($transaction->amount) }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($transaction->leadger->type == 1 || $transaction->leadger->type == 3)
                                {{ single_price($debit_total - $credit_total) }}
                            @else
                                {{ single_price($credit_total - $debit_total) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5" class="head_color text-right nowrap">{{ trans("account.cumulated_balance") }}</td>
                    <td class="head_color text-right nowrap">
                        {{ single_price($total_debit_amount) }}
                    </td>
                    <td class="head_color text-right nowrap">
                        {{ single_price($total_credit_amount) }}
                    </td>
                    <td class="head_color text-right nowrap">
                        @if ($transaction->leadger->type == 1 || $transaction->leadger->type == 3)
                            {{ single_price($total_debit_amount - $total_credit_amount) }}
                        @else
                            {{ single_price($total_credit_amount - $total_debit_amount) }}
                        @endif
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
